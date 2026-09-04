<?php

declare(strict_types=1);

namespace Bb\ConsentBanner\Frontend;

use Bb\ConsentBanner\DataProcessing\ConsentBannerProcessor;
use Bb\ConsentBanner\Utility\CookieUtility;
use Doctrine\DBAL\Exception;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;
use TYPO3\CMS\Core\Attribute\AsAllowedCallable;
use TYPO3\CMS\Core\Core\RequestId;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Renders the tracking bootstrap as early, uncached <head> content
 * (page.headerData USER_INT), before the actual tags fire:
 *   - Google Consent Mode: gtag stub + `consent default` (denied) + a
 *     `consent update` for returning visitors + optional GTM loader.
 *   - Matomo: _paq requireConsent/requireCookieConsent (+ setConsentGiven for
 *     returning visitors) + tracker or Matomo Tag Manager loader.
 *
 * The live updates on accept/withdraw happen in CbLoader.js.
 */
class ConsentModeRenderer
{
    private const string COMPONENT_TABLE = 'tx_consentbanner_domain_model_consent_components';
    private const string BANNER_TABLE = 'tx_consentbanner_domain_model_banner';

    /**
     * @var array<string, string>
     */
    private const array DEFAULT_SIGNALS = [
        'ad_storage' => 'denied',
        'ad_user_data' => 'denied',
        'ad_personalization' => 'denied',
        'analytics_storage' => 'denied',
        'functionality_storage' => 'denied',
        'personalization_storage' => 'denied',
        'security_storage' => 'granted',
    ];

    /**
     * TYPO3 14 laesst als userFunc nur noch Methoden zu, die dieses Attribut
     * tragen (AllowedCallableAssertion); ohne es endet jeder Seitenaufruf mit
     * einer AllowedCallableException. Unter TYPO3 13 gibt es die
     * Attribut-Klasse nicht - das schadet nicht, denn Attribute werden erst
     * beim Auslesen per Reflection aufgeloest, und dort liest sie niemand aus.
     */
    #[AsAllowedCallable]
    public function render(string $content, array $conf, ?ServerRequestInterface $request = null): string
    {
        $site = $request?->getAttribute('site');
        if (!$site instanceof Site) {
            return '';
        }
        $rootPageId = $site->getRootPageId();

        $tracking = $this->getBannerTracking($rootPageId);
        $preferences = $this->getPreferences();

        $lines = array_merge(
            $this->buildGoogle($rootPageId, $tracking['gtm'], $preferences),
            $this->buildMatomo($rootPageId, $tracking, $preferences)
        );

        if ($lines === []) {
            return '';
        }

        $nonce = $this->resolveNonceValue();
        $nonceAttr = $nonce !== '' ? ' nonce="' . htmlspecialchars($nonce) . '"' : '';

        return '<script' . $nonceAttr . '>' . implode("\n", $lines) . '</script>';
    }

    /**
     * @param array<string, mixed> $preferences
     * @return string[]
     */
    private function buildGoogle(int $rootPageId, string $gtmId, array $preferences): array
    {
        // Ohne GTM-Container-ID gibt es keinen Consumer fuer den gtag-Stub /
        // consent default (der Google-Pfad laedt ausschliesslich GTM) -> abbrechen.
        if ($gtmId === '') {
            return [];
        }

        $components = $this->getComponentsByType($rootPageId, 'google_consent_mode');

        $granted = [];
        foreach ($components as $component) {
            if (($preferences[$component['id']] ?? false) !== true) {
                continue;
            }
            foreach (GeneralUtility::trimExplode(',', $component['signals'], true) as $signal) {
                if (isset(self::DEFAULT_SIGNALS[$signal])) {
                    $granted[$signal] = 'granted';
                }
            }
        }

        $lines = [];
        $lines[] = 'window.dataLayer = window.dataLayer || [];';
        $lines[] = 'function gtag(){dataLayer.push(arguments);}';
        $lines[] = "gtag('consent','default'," . json_encode(self::DEFAULT_SIGNALS + ['wait_for_update' => 500]) . ');';
        if ($granted !== []) {
            $lines[] = "gtag('consent','update'," . json_encode($granted) . ');';
        }
        $lines[] = "(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});"
            . "var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';"
            . "j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;"
            . "f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer'," . json_encode($gtmId) . ');';

        return $lines;
    }

    /**
     * @param array<string, string> $tracking
     * @param array<string, mixed> $preferences
     * @return string[]
     */
    private function buildMatomo(int $rootPageId, array $tracking, array $preferences): array
    {
        $hasTracker = $tracking['matomoUrl'] !== '' && $tracking['matomoSiteId'] !== '';
        $hasMtm = $tracking['matomoMtmUrl'] !== '';
        if (!$hasTracker && !$hasMtm) {
            return [];
        }

        $components = $this->getComponentsByType($rootPageId, 'matomo');
        $consented = false;
        foreach ($components as $component) {
            if (($preferences[$component['id']] ?? false) === true) {
                $consented = true;
                break;
            }
        }

        $lines = [];
        if ($hasMtm) {
            $lines[] = "var _mtm = window._mtm = window._mtm || [];";
            $lines[] = "_mtm.push({'mtm.startTime': (new Date().getTime()), 'event': 'mtm.Start'});";
        }
        $lines[] = "var _paq = window._paq = window._paq || [];";
        $lines[] = "_paq.push(['requireConsent']);";
        $lines[] = "_paq.push(['requireCookieConsent']);";
        if ($consented) {
            $lines[] = "_paq.push(['setConsentGiven']);";
            $lines[] = "_paq.push(['setCookieConsentGiven']);";
        }

        if ($hasMtm) {
            $lines[] = "(function(){var d=document,g=d.createElement('script'),s=d.getElementsByTagName('script')[0];"
                . "g.async=true;g.src=" . json_encode($tracking['matomoMtmUrl']) . ";s.parentNode.insertBefore(g,s);})();";
        } else {
            $url = rtrim($tracking['matomoUrl'], '/') . '/';
            $lines[] = "_paq.push(['trackPageView']);";
            $lines[] = "_paq.push(['enableLinkTracking']);";
            $lines[] = "(function(){var u=" . json_encode($url) . ";"
                . "_paq.push(['setTrackerUrl', u+'matomo.php']);"
                . "_paq.push(['setSiteId', " . json_encode($tracking['matomoSiteId']) . "]);"
                . "var d=document,g=d.createElement('script'),s=d.getElementsByTagName('script')[0];"
                . "g.async=true;g.src=u+'matomo.js';s.parentNode.insertBefore(g,s);})();";
        }

        return $lines;
    }

    /**
     * @return array<int, array{id: string, signals: string}>
     */
    private function getComponentsByType(int $rootPageId, string $type): array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable(self::COMPONENT_TABLE);

        try {
            $rows = $queryBuilder
                ->select('component_id', 'consent_mode_signals')
                ->from(self::COMPONENT_TABLE)
                ->where(
                    $queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($rootPageId, Connection::PARAM_INT)),
                    $queryBuilder->expr()->eq('integration_type', $queryBuilder->createNamedParameter($type)),
                    $queryBuilder->expr()->neq('component_id', $queryBuilder->createNamedParameter(''))
                )
                ->executeQuery()
                ->fetchAllAssociative();
        } catch (Exception) {
            return [];
        }

        return array_map(
            static fn(array $row): array => ['id' => (string)$row['component_id'], 'signals' => (string)$row['consent_mode_signals']],
            $rows
        );
    }

    /**
     * @return array{gtm: string, matomoUrl: string, matomoSiteId: string, matomoMtmUrl: string}
     */
    private function getBannerTracking(int $rootPageId): array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable(self::BANNER_TABLE);

        try {
            $row = $queryBuilder
                ->select('gtm_container_id', 'matomo_url', 'matomo_site_id', 'matomo_mtm_url')
                ->from(self::BANNER_TABLE)
                ->where(
                    $queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($rootPageId, Connection::PARAM_INT))
                )
                ->orderBy('sys_language_uid')
                ->setMaxResults(1)
                ->executeQuery()
                ->fetchAssociative();
        } catch (Exception) {
            $row = false;
        }

        return [
            'gtm' => is_array($row) ? trim((string)$row['gtm_container_id']) : '',
            'matomoUrl' => is_array($row) ? trim((string)$row['matomo_url']) : '',
            'matomoSiteId' => is_array($row) ? trim((string)$row['matomo_site_id']) : '',
            'matomoMtmUrl' => is_array($row) ? trim((string)$row['matomo_mtm_url']) : '',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function getPreferences(): array
    {
        $raw = CookieUtility::getCookieValue(ConsentBannerProcessor::$cName);
        if ($raw === '') {
            return [];
        }
        $preferences = json_decode($raw, true);

        return is_array($preferences) ? $preferences : [];
    }

    private function resolveNonceValue(): string
    {
        try {
            return GeneralUtility::makeInstance(RequestId::class)->nonce->consume();
        } catch (Throwable) {
            return '';
        }
    }

}
