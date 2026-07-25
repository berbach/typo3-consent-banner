<?php

declare(strict_types=1);

namespace Bb\ConsentBanner\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Backend AJAX endpoint that searches the bundled service database
 * (cookie_database.json) for the service autocomplete FormEngine element.
 *
 * Only the fields service_name, service_identifier and service_aliases are
 * searched (case-insensitive, substring). Matching services are returned as
 * their complete JSON block so the client can fill all sibling fields and the
 * inline cookie rows without a second request.
 */
class ServiceDatabaseController
{
    protected const DATABASE_PATH = 'EXT:consent_banner/Resources/Public/cookie_database.json';
    protected const MAX_RESULTS = 20;

    public function searchAction(ServerRequestInterface $request): ResponseInterface
    {
        $query = trim((string)($request->getQueryParams()['q'] ?? ''));
        $services = $this->loadServices();

        if ($query === '') {
            return new JsonResponse(['results' => []]);
        }

        $needle = mb_strtolower($query);
        $results = [];
        foreach ($services as $service) {
            if ($this->matches($service, $needle)) {
                $results[] = $service;
            }
            if (count($results) >= self::MAX_RESULTS) {
                break;
            }
        }

        return new JsonResponse(['results' => $results]);
    }

    /**
     * @param array<string, mixed> $service
     */
    protected function matches(array $service, string $needle): bool
    {
        $haystack = [];
        $haystack[] = (string)($service['service_name'] ?? '');
        $haystack[] = (string)($service['service_identifier'] ?? '');
        foreach ((array)($service['service_aliases'] ?? []) as $alias) {
            $haystack[] = (string)$alias;
        }

        foreach ($haystack as $value) {
            if ($value !== '' && str_contains(mb_strtolower($value), $needle)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function loadServices(): array
    {
        $absolutePath = GeneralUtility::getFileAbsFileName(self::DATABASE_PATH);
        if ($absolutePath === '' || !is_file($absolutePath)) {
            return [];
        }

        $contents = file_get_contents($absolutePath);
        if ($contents === false) {
            return [];
        }

        try {
            $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return [];
        }

        $services = $data['services'] ?? [];
        return is_array($services) ? array_values(array_filter($services, 'is_array')) : [];
    }
}
