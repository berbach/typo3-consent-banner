<?php

use Bb\ConsentBanner\Controller\ManagementController;
/**
 * Definitions for modules provided by EXT:examples
 */
return [
    'consentbanner_management' => [
        'parent' => 'site',
        'position' => ['after' => 'web_ts'],
        'access' => 'admin',
        'workspaces' => 'live',
        'path' => '/module/consent-banner',
        /*
         * Nur die Datei, ohne Schluessel: TYPO3 haengt selbst
         * ":mlang_tabs_tab", ":mlang_labels_tabdescr" und
         * ":mlang_labels_tablabel" an (BaseModule::createFromConfiguration).
         * Mit einem Schluessel darin entsteht "...xlf:module.label:mlang_tabs_tab",
         * und weil es den nicht gibt, stand im Menue der Pfad zur Sprachdatei.
         */
        'labels' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf',
        'extensionName' => 'ConsentBanner',
        'iconIdentifier' => 'module-cookie',
        'controllerActions' => [
            ManagementController::class => [
                'banner', 'consents', 'delete'
            ],
        ],
    ],
];