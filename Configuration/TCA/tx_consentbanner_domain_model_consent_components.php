<?php

return [
    'ctrl' => [
        'title' => 'Consent components',
        'label' => 'component_title',
        'sortby' => 'sorting',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'delete' => 'deleted',
        'adminOnly' => true,
        'versioningWS' => false,
        'hideAtCopy' => true,
        'searchFields' => '',
        'typeicon_classes' => [
            'default' => 'module-cookie'
        ],
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
        'transOrigPointerField' => 'l10n_parent',
        'languageField' => 'sys_language_uid',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'copyAfterDuplFields' => 'sys_language_uid',
        'useColumnsForDefaultValues' => 'sys_language_uid',
        'translationSource' => 'l10n_source',
        'security' => [
            'ignorePageTypeRestriction' => true,
        ],
    ],
    'types' => [
        '0' => [
            'showitem' => '
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                    --palette--;;header,
                    --palette--;;content,
                    --palette--;;ce_target,
                --div--;Integration,
                    --palette--;;integration,
                --div--;LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:tab.javascript,
                    --palette--;;javascript,
                --div--;LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:tab.placeholder,
                    --palette--;;placeholder,
                --div--;LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:tab.cookie_information,
                    --palette--;;service,
                    service_cookies,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,
                    --palette--;;language,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
                    --palette--;;hidden,    
                '
        ]
    ],
    'palettes' => [
        'header' => [
            'showitem' => '
                component_title,
            ',
        ],
        'content' => [
            'showitem' => '
                component_description,
            ',
        ],
        'ce_target' => [
            'showitem' => '
                component_ce_target
            ',
        ],
        'integration' => [
            'showitem' => '
                integration_type,
                --linebreak--,
                consent_mode_signals
            ',
        ],
        'placeholder' => [
            'showitem' => '
                placeholder_title,
                --linebreak--,
                placeholder_description,
            ',
        ],
        'language' => [
            'showitem' => '
                sys_language_uid,
                l10n_parent
            ',
        ],
        'hidden' => [
            'showitem' => '
                hidden
            ',
        ],
        'javascript' => [
            'showitem' => '
                    accepted_script,
                --linebreak--,
                    rejected_script
            ',
        ],
        'service' => [
            'showitem' => '
                    service_name,
                --linebreak--,
                    service_provider, service_publisher,
                --linebreak--,
                    service_privacy_link,
                --linebreak--,
                    service_data_collected,
            ',
        ],
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'language',
            ],
        ],
        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'default' => 0,
                'items' => [
                    ['label' => '', 'value' => 0],
                ],
                'foreign_table' => 'tx_consentbanner_domain_model_consent_components',
                'foreign_table_where' => 'AND {#tx_consentbanner_domain_model_consent_components}.{#pid}=###CURRENT_PID### AND {#tx_consentbanner_domain_model_consent_components}.{#sys_language_uid} IN (-1,0)',
            ],
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],

        'l10n_source' => [
            'config' => [
                'type' => 'passthrough'
            ]
        ],

        'hidden' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.visible',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'items' => [
                    [
                        'label' => '',
                        'invertStateDisplay' => true,
                    ]
                ],
            ],
        ],

        'component_title' => [
            'exclude' => true,
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.component_title',
            'config' => [
                'type' => 'input',
                'size' => 50,
                'eval' => 'trim',
                'required' => true
            ],
        ],

        'component_description' => [
            'exclude' => true,
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.component_description',
            'config' => [
                'type' => 'text',
                'eval' => 'trim',
                'cols' => 50,
                'rows' => 10
            ]
        ],

        'component_id' => [
            'config' => [
                'type' => 'passthrough'
            ],
        ],

        'component_hash' => [
            'config' => [
                'type' => 'passthrough'
            ],
        ],

        'component_ce_target' => [
            'exclude' => true,
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.component_ce_target',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'items' => [],
                'itemsProcFunc' => Bb\ConsentBanner\Utility\TCASelectItemUtility::class . '->getAllContentElements',
            ]
        ],

        'integration_type' => [
            'exclude' => true,
            'label' => 'Integrations-Typ',
            'description' => 'Wie diese Component im Frontend eingebunden wird.',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'default' => 'iframe',
                'items' => [
                    ['label' => 'Inhaltselement / Iframe (Placeholder)', 'value' => 'iframe'],
                    ['label' => 'Google Consent Mode (gtag)', 'value' => 'google_consent_mode'],
                    ['label' => 'Matomo', 'value' => 'matomo'],
                    ['label' => 'Script laden', 'value' => 'script'],
                ],
            ],
        ],

        'consent_mode_signals' => [
            'exclude' => true,
            'label' => 'Google Consent Mode Signale',
            'description' => 'Welche gtag-Consent-Signale diese Component bei Einwilligung auf "granted" setzt.',
            'displayCond' => 'FIELD:integration_type:=:google_consent_mode',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectCheckBox',
                'items' => [
                    ['label' => 'analytics_storage', 'value' => 'analytics_storage'],
                    ['label' => 'ad_storage', 'value' => 'ad_storage'],
                    ['label' => 'ad_user_data', 'value' => 'ad_user_data'],
                    ['label' => 'ad_personalization', 'value' => 'ad_personalization'],
                    ['label' => 'functionality_storage', 'value' => 'functionality_storage'],
                    ['label' => 'personalization_storage', 'value' => 'personalization_storage'],
                    ['label' => 'security_storage', 'value' => 'security_storage'],
                ],
            ],
        ],

        'placeholder_title' => [
            'exclude' => true,
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.placeholder_title',
            'config' => [
                'type' => 'input',
                'size' => 50,
                'eval' => 'trim'
            ],
        ],

        'placeholder_description' => [
            'exclude' => true,
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.placeholder_description',
            'config' => [
                'type' => 'text',
                'eval' => 'trim',
                'rows' => 5
            ]
        ],

        'accepted_script' => [
            'exclude' => true,
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.accepted_script',
            'config' => [
                'type' => 'text',
                'renderType' => 'codeEditor',
                'format' => 'javascript',
                'eval' => 'trim',
                'cols' => 80,
                'rows' => 20
            ]
        ],

        'rejected_script' => [
            'exclude' => true,
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.rejected_script',
            'config' => [
                'type' => 'text',
                'renderType' => 'codeEditor',
                'format' => 'javascript',
                'eval' => 'trim',
                'cols' => 80,
                'rows' => 20
            ]
        ],

        'service_name' => [
            'exclude' => true,
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.service_name',
            'description' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.service_name.description',
            'config' => [
                'type' => 'input',
                'renderType' => 'serviceAutocomplete',
                'size' => 50,
                'eval' => 'trim',
            ],
        ],

        'service_provider' => [
            'exclude' => true,
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.service_provider',
            'config' => [
                'type' => 'input',
                'size' => 50,
                'eval' => 'trim',
            ],
        ],

        'service_privacy_link' => [
            'exclude' => true,
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.service_privacy_link',
            'config' => [
                'type' => 'link',
                'size' => 50,
                'allowedTypes' => ['url', 'page'],
            ],
        ],

        'service_publisher' => [
            'exclude' => true,
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.service_publisher',
            'config' => [
                'type' => 'input',
                'size' => 50,
                'eval' => 'trim',
            ],
        ],

        'service_data_collected' => [
            'exclude' => true,
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.service_data_collected',
            'config' => [
                'type' => 'text',
                'eval' => 'trim',
                'cols' => 50,
                'rows' => 4,
            ],
        ],

        'service_cookies' => [
            'exclude' => true,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.cookies',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_consentbanner_domain_model_cookie',
                'foreign_field' => 'component',
                'foreign_sortby' => 'sorting_foreign',
                'foreign_label' => 'cookie_name',
                'appearance' => [
                    'collapseAll' => true,
                    'expandSingle' => true,
                    'useSortable' => true,
                    'showSynchronizationLink' => false,
                    'showPossibleLocalizationRecords' => false,
                    'showAllLocalizationLink' => false,
                    'enabledControls' => [
                        'info' => false,
                    ],
                    'levelLinksPosition' => 'top',
                ],
            ],
        ],

        'group_id' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
    ],
];
