<?php
return [
    'ctrl' => [
        'title' => 'Consent groups',
        'label' => 'group_title',
        'sortby' => 'sorting',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'delete' => 'deleted',
        'adminOnly' => true,
        'versioningWS' => false,
        'hideAtCopy' => true,
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
                    group_title,group_description,
                --div--;LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:tab.components,
                    group_components,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,
                    --palette--;;language,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
                    --palette--;;hidden,        
                '
        ]
    ],
    'palettes' => [
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
                'foreign_table' => 'tx_consentbanner_domain_model_consent_groups',
                'foreign_table_where' => 'AND {#tx_consentbanner_domain_model_consent_groups}.{#pid}=###CURRENT_PID### AND {#tx_consentbanner_domain_model_consent_groups}.{#sys_language_uid} IN (-1,0)',
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

        'group_id' => [
            'config' => [
                'type' => 'passthrough'
            ],
        ],

        'group_hash' => [
            'config' => [
                'type' => 'passthrough'
            ],
        ],

        'group_title' => [
            'exclude' => true,
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.group_title',
            'config' => [
                'type' => 'input',
                'size' => 50,
                'eval' => 'trim',
                'required' => true
            ],
        ],


        'group_description' => [
            'exclude' => true,
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.group_description',
            'config' => [
                'type' => 'text',
                'eval' => 'trim',
                'cols' => 100,
                'rows' => 10
            ]
        ],



        'group_components' => [
            'exclude' => false,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.group_components',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_consentbanner_domain_model_consent_components',
                'foreign_field' => 'group_id',
                'foreign_sortby' => 'sorting_foreign',
                'foreign_label' => 'component_title',
                'foreign_match_fields' => [
                    'group_parent' => 'other_group',
                ],
                'maxitems' => 5,
                'appearance' => [
                    'newRecordLinkTitle' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.group_components.add',
                    'useSortable' => true,
                    'levelLinksPosition' => 'top',
                    'enabledControls' => ['info' => false],
                    'collapseAll' => 1,
                    'expandSingle' => 1,
                ],


            ],
        ],

        'banner_id' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
    ],
];