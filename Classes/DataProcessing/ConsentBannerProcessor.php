<?php

namespace Bb\ConsentBanner\DataProcessing;

use Bb\ConsentBanner\Domain\Model\Banner;
use Bb\ConsentBanner\Utility\CookieUtility;
use Bb\ConsentBanner\Domain\Repository\BannerRepository;
use Bb\ConsentBanner\Utility\Counter;
use Doctrine\DBAL\Exception;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\Exception\AspectNotFoundException;
use TYPO3\CMS\Core\Core\RequestId;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Page\AssetCollector;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;
use TYPO3\CMS\Frontend\ContentObject\Exception\ContentRenderingException;

class ConsentBannerProcessor implements DataProcessorInterface
{
    /**
     * @var string
     */
    public static string $cName = 'BbConsentPreferences';
    /**
     * @var ContentObjectRenderer
     */
    protected ContentObjectRenderer $cObj;

    /**
     * @param ContentObjectRenderer $cObj
     * @param array $contentObjectConfiguration
     * @param array $processorConfiguration
     * @param array $processedData
     * @return array
     * @throws AspectNotFoundException
     * @throws Exception
     * @throws ContentRenderingException
     */
    public function process(ContentObjectRenderer $cObj, array $contentObjectConfiguration, array $processorConfiguration, array $processedData): array
    {
        $this->cObj = $cObj;
        $requestSite = $this->getTypo3Request()->getAttribute('site');
        $consentPreferences = CookieUtility::getCookieValue(self::$cName);
        $bannerRepository = GeneralUtility::makeInstance(BannerRepository::class);
        /* @var Banner $banner */
        $banner = $bannerRepository->findByRootPageId($requestSite->getRootPageId(), $this->getCurrentLanguage());

        if (!$consentPreferences) {
            $consentAccepted = false;
        }else{
            $consentAccepted = true;
        }

        $tempBanner = [];
        if(!empty($banner)){
            $tempBanner = [

                'banner' => [
                    'id'                    => $banner->getBannerId(),
                    'hash'                  => $banner->getBannerHash(),
                    'title'                 => $banner->getBannerTitle(),
                    'description'           => $banner->getBannerDescription(),
                    'active'                => $banner->getBannerActive(),
                    'version'               => $banner->getBannerVersion(),
                ],
                'footerNavigation'      => $this->addFooterNavigation($banner->getBannerNavigation()),
                'displayTexts' => [
                    'buttons'     => [
                        'acceptAll'             => !empty($banner->getAcceptAllText()) ? $banner->getAcceptAllText() : $this->getTranslate('accept_all_text'),
                        'acceptEssential'       => !empty($banner->getAcceptEssentialText()) ? $banner->getAcceptEssentialText() : $this->getTranslate('accept_essential_text'),
                        'close'                 => $this->getTranslate('close_text'),
                        'saveAndClose'          => !empty($banner->getSaveAndCloseText()) ? $banner->getSaveAndCloseText() : $this->getTranslate('save_and_close_text'),
                        'confirmSelection'      => !empty($banner->getConfirmSelectionText()) ? $banner->getConfirmSelectionText() : $this->getTranslate('confirm_selection_text'),
                        'advancedSettings'      => !empty($banner->getAdvancedSettingsText()) ? $banner->getAdvancedSettingsText() : $this->getTranslate('advanced_settings_text'),
                        'showInfo'              => !empty($banner->getCookieInfosShowText()) ? $banner->getCookieInfosShowText() : $this->getTranslate('cookie_infos_show_text'),
                        'closeInfo'             => !empty($banner->getCookieInfosCloseText()) ? $banner->getCookieInfosCloseText() : $this->getTranslate('cookie_infos_close_text'),
                    ],
                    'cookie'      => [
                        'name'          => !empty($banner->getCookieNameText()) ? $banner->getCookieNameText() : $this->getTranslate('cookie_name_text'),
                        'lifetime'      => !empty($banner->getCookieLifetimeText()) ? $banner->getCookieLifetimeText() : $this->getTranslate('cookie_lifetime_text'),
                        'provider'      => !empty($banner->getCookieProviderText()) ? $banner->getCookieProviderText() : $this->getTranslate('cookie_provider_text'),
                        'purpose'       => !empty($banner->getCookiePurposeText()) ? $banner->getCookiePurposeText() : $this->getTranslate('cookie_purpose_text'),
                        'description'   => !empty($banner->getCookieDescriptionText()) ? $banner->getCookieDescriptionText() : $this->getTranslate('cookie_description_text'),
                    ],
                    'cookieTableHeading' => $this->getTranslate('cookie_table_heading'),
                    'serviceMeta' => [
                        'publisher'     => $this->getTranslate('meta_publisher_label'),
                        'provider'      => $this->getTranslate('meta_provider_label'),
                        'dataCollected' => $this->getTranslate('meta_data_collected_label'),
                        'privacy'       => $this->getTranslate('meta_privacy_label'),
                    ],

                ],
                'openerVariant'         => $banner->getPrivacySettingsVariant(), //10 Text link | 20 Button Widget
                'openerData'            => $this->addBannerOpenerVariant($banner->getPrivacySettingsVariant(), [
                    '10' => [
                        'targetFooterNavigation'    => $banner->getTargetFooterNavigation(),
                        'textLinkPosition'          => $banner->getTextLinkPosition(),
                        'textLinkText'              => !empty($banner->getTextLinkText()) ? $banner->getTextLinkText() : $this->getTranslate('text_link_text'),
                    ],
                    '20' => [
                        'buttonWidgetPosition'      => $banner->getButtonWidgetPosition(),
                        'buttonWidgetText'          => !empty($banner->getButtonWidgetText()) ? $banner->getButtonWidgetText() : $this->getTranslate('button_widget_text'),
                    ]
                ]),
                'groups' => $this->addGroups(
                    [
                        'id'                => $banner->getEssentialGroupId(),
                        'hash'              => $banner->getEssentialGroupHash(),
                        'title'             => !empty($banner->getEssentialTitle()) ? $banner->getEssentialTitle() : $this->getTranslate('essential_title_text'),
                        'description'       => $banner->getEssentialDescription(),
                        'components'        => $this->addComponents($banner->getEssentialComponents(), $banner->getEssentialGroupId()),
                        'lockedAndActive'   => true
                    ],
                    $banner->getConsentOtherGroups()
                ),
                'layout'                => $banner->getBannerLayout(), // cb-bottom || cb-overlay
                'lifetimes' => [
                    'banner'            => $banner->getLifetimeBanner(), // 14 || 21 || 28 Days
                    'userConsent'       => $banner->getLifetimeUserConsent() // 365 || 730 || 1095 Days
                ],
                'consentAccepted'       => $consentAccepted,
                'cName'                 => self::$cName,
            ];
        }

        GeneralUtility::makeInstance(AssetCollector::class)
            ->addInlineJavaScript(
                'banner_data',
                json_encode($tempBanner),
                ['nonce' => $this->resolveNonceValue(), 'id' => 'bbBannerData', 'type' => 'application/json', 'crossorigin' => 'anonymous'],
                ['priority' => true]
            );

        $processedData['banner']['data'] = $tempBanner;
        return $processedData;
    }

    /**
     * @throws AspectNotFoundException
     * @throws Exception
     */
    protected function addFooterNavigation(?string $navigationIds): array
    {
        $pageIds = GeneralUtility::trimExplode(',', $navigationIds, true);
        $menuItems = [];
        if (!empty($pageIds)){
            foreach ($pageIds as $pageId){
                $pageRecord = $this->getRecord('pages', $pageId, 'uid, pid, ' . $GLOBALS['TCA']['pages']['ctrl']['languageField'] . ', nav_title, title');
                //$pageRecord = BackendUtility::getRecord('pages', $pageId);
                $menuItems[] = [
                    'uid' => $pageRecord['uid'],
                    'title' => !empty($pageRecord['nav_title']) ? $pageRecord['nav_title'] : $pageRecord['title'],
                    'url' => $this->cObj->createUrl(['parameter' => $pageId])
                ];
            }
        }
        return $menuItems;
    }

    /**
     * @param int $variant
     * @param array $openerVariants
     * @return array
     */
    protected function addBannerOpenerVariant(int $variant, array $openerVariants):array
    {
        if (isset($openerVariants[$variant])) {
            $openerVariants[$variant]['type'] = $variant;
            return $openerVariants[$variant];
        }
        return [];
    }



    /**
     * @param object $components
     * @param int|string $groupId
     * @return array
     */
    protected function addComponents(object $components, int|string $groupId): array
    {
        if ($components->count() > 0){
            $counter = GeneralUtility::makeInstance(Counter::class, 10, 10);
            $tempComponents = [];
            foreach ($components as $component) {
                $tempComponents[$counter->count()] = [
                    'id' => $component->getComponentId(),
                    'hash' => $component->getComponentHash(),
                    'groupId' => $groupId,
                    'title' => $component->getComponentTitle(),
                    'description' => $component->getComponentDescription(),
                    'integrationType' => $component->getIntegrationType(),
                    'signals' => $component->getConsentModeSignals(),
                    'acceptedScript' => $component->getAcceptedScript(),
                    'rejectedScript' => $component->getRejectedScript(),
                    'service' => [
                        'name' => $component->getServiceName(),
                        'provider' => $component->getServiceProvider(),
                        'privacyLink' => $component->getServicePrivacyLink(),
                        'publisher' => $component->getServicePublisher(),
                        'dataCollected' => $component->getServiceDataCollected(),
                    ],
                    'cookies' => $this->addCookies($component)
                ];
                $counter->increment();
            }
            return $tempComponents;
        }
        return [];
    }

    /**
     * Collects the cookies of a component as a plain array for the frontend.
     *
     * @return array<int, array{name: string, type: string, purpose: string, lifetime: string, description: string}>
     */
    protected function addCookies(object $component): array
    {
        $cookies = [];
        foreach ($component->getServiceCookies() as $cookie) {
            $cookies[] = [
                'name' => $cookie->getCookieName(),
                'type' => $cookie->getCookieType(),
                'purpose' => $cookie->getCookiePurpose(),
                'lifetime' => $cookie->getCookieLifetime(),
                'description' => $cookie->getCookieDescription(),
            ];
        }
        return $cookies;
    }

    /**
     * @param array $essentialGroup
     * @param object $otherGroups
     * @return array
     */
    protected function addGroups(array $essentialGroup, object $otherGroups): array
    {
        $counter = GeneralUtility::makeInstance(Counter::class, 10, 10);
        $tempGroups = [];

        if ($otherGroups->count() > 0){
            $tempGroups[$counter->count()] = $essentialGroup;
            $counter->increment();
            foreach ($otherGroups as $group){
                $tempGroup = [
                    'id'                => $group->getGroupId(),
                    'hash'              => $group->getGroupHash(),
                    'title'             => $group->getGroupTitle(),
                    'description'       => $group->getGroupDescription(),
                    'components'        => $this->addComponents($group->getGroupComponents(), $group->getGroupId()),
                    'lockedAndActive'   => false
                ];

                $tempGroups[$counter->count()] = $tempGroup;
                $counter->increment();
            }
        }

        return $tempGroups;
    }

    /**
     * Get the record including possible translations
     * @throws Exception
     * @throws AspectNotFoundException
     */
    protected function getRecord(string $table, int $uid, string $fields = '*'): array
    {
        if (MathUtility::canBeInterpretedAsInteger($uid)) {
            $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
                ->getQueryBuilderForTable($table);
            try {
                $row = $queryBuilder
                    ->select(...GeneralUtility::trimExplode(',', $fields, true))
                    ->from($table)
                    ->where(
                        $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($uid, Connection::PARAM_INT))
                    )
                    ->executeQuery()
                    ->fetchAssociative();

                if ($row) {
                    $row = GeneralUtility::makeInstance(PageRepository::class)->getLanguageOverlay($table, $row, $this->getContext()->getAspect('language'));
                }

                if (is_array($row) && !empty($row)) {
                    return $row;
                }
            } catch (Exception) {
                // do nothing
            }
        }
        return [];
    }

    /**
     * Get current language
     *
     * @return int $languageId
     */
    protected function getCurrentLanguage(): int
    {
        $languageId = 0;
        try {
            $languageId = $this->getContext()->getPropertyFromAspect('language', 'contentId');
        } catch (AspectNotFoundException) {
            // do nothing
        }
        return (int)$languageId;
    }

    protected function getTranslate(string $key): string
    {
        return LocalizationUtility::translate('LLL:EXT:consent_banner/Resources/Private/Language/locallang.xlf:'. trim($key));
    }

    protected function getContext(): Context
    {
        return GeneralUtility::makeInstance(Context::class);
    }

    /**
     * @throws ContentRenderingException
     */
    protected function getTypo3Request(): ServerRequestInterface
    {
        return $this->cObj->getRequest();
    }

    protected function resolveNonceValue(): string
    {
        // Nonce muss aus dem RequestId-Service kommen: das Request-Attribut 'nonce'
        // wird erst von der csp-headers-Middleware (after prepare-tsfe-rendering)
        // gesetzt und ist im DataProcessor-Kontext daher noch nicht verfügbar.
        return GeneralUtility::makeInstance(RequestId::class)->nonce->consume();
    }



}
