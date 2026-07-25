<?php

use Bb\ConsentBanner\Controller\ServiceDatabaseController;

/**
 * Backend AJAX routes. Registered automatically by the core from
 * Configuration/Backend/AjaxRoutes.php. Route identifiers are prefixed with
 * "ajax_" internally; use the bare identifier with the backend UriBuilder.
 */
return [
    'consentbanner_service_search' => [
        'path' => '/consentbanner/service-search',
        'target' => ServiceDatabaseController::class . '::searchAction',
    ],
];
