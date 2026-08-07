<?php

declare(strict_types=1);

namespace Bb\ConsentBanner\Hook;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Page\PageRenderer;

/**
 * Loads the service autocomplete JS module on every backend page render.
 *
 * The serviceAutocomplete FormEngine element also declares the module per
 * element, which is enough when the field is part of the initial DOM (e.g. the
 * stand-alone component edit form). However, when a component is edited as a
 * deeply nested, lazily AJAX-loaded inline record (Banner → group → component),
 * the field is injected after the initial render and its per-element module
 * import is not reliably executed. Loading the module globally in the backend
 * keeps its MutationObserver active in the edit document, so the field is
 * initialized whenever it appears, regardless of how it was rendered.
 *
 * Registered as a PageRenderer render-preProcess hook (fires for every backend
 * PageRenderer render, including the record-edit content frame). Guarded to
 * backend requests so the frontend never pulls the backend-only module.
 */
class BackendAssetHook
{
    /**
     * @param array<string, mixed> $params
     */
    public function registerAssets(array &$params, PageRenderer $pageRenderer): void
    {
        $request = $GLOBALS['TYPO3_REQUEST'] ?? null;
        if (!$request instanceof ServerRequestInterface) {
            return;
        }
        if (!ApplicationType::fromRequest($request)->isBackend()) {
            return;
        }
        $pageRenderer->loadJavaScriptModule('@bb/consentbanner/ServiceAutocomplete.js');
        $pageRenderer->addCssFile('EXT:consent_banner/Resources/Public/Css/Backend.css');
    }
}
