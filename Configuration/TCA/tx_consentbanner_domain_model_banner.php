<?php

use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
return [
    'ctrl' => [
        'title' => 'Consent Banner',
        'label' => 'banner_title',
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
                --div--;LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:tab.banner,
                    --palette--;;banner,
                    --palette--;;linksBannerMenu,
                    --palette--;;hidden,
                --div--;LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:tab.text_for_labels,
                    --palette--;;buttonsLabelText,
                    --palette--;;cookieInfoLabels,
                    --palette--;;cookieMoreInfoLabels,
                --div--;LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:tab.essential_group,
                    --palette--;;essential,
                    --palette--;;essentialConsents,
                --div--;LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:tab.other_group,
                    --palette--;;otherGroup,
                --div--;LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:tab.banner_setting,
                    --palette--;;bannerLayout,
                    --palette--;;bannerPrivacySettingsVariant,
                    --palette--;;bannerTextLinkVariant,
                    --palette--;;bannerButtonWidgetVariant,
                    --palette--;;cookieLifeTime,
                    --palette--;;language,
                --div--;Tracking / Consent Mode,
                    --palette--;;tracking,
                '
        ]
    ],
    'palettes' => [
        'banner' => [
            'showitem' => '
                banner_title,
                    --linebreak--,
                banner_description,
            ',
        ],

        'tracking' => [
            'showitem' => '
                gtm_container_id,
                --linebreak--,
                matomo_url, matomo_site_id,
                --linebreak--,
                matomo_mtm_url
            ',
        ],

        'cookieLifeTime' => [
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:palette.cookieLifeTime',
            'showitem' => '
                lifetime_banner, lifetime_user_consent
            ',
        ],

        'buttonsLabelText' => [
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:palette.buttonsLabelText',
            'showitem' => '
                accept_all_text, confirm_selection_text,
                    --linebreak--, 
                save_and_close_text, advanced_settings_text,
                    --linebreak--, 
                accept_essential_text
            ',
        ],

        'bannerLayout' => [
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:palette.bannerLayout',
            'showitem' => '
                banner_layout
            '
        ],

        'bannerPrivacySettingsVariant' => [
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:palette.bannerPrivacySettingsVariant',
            'showitem' => '
                privacy_settings_variant
            '
        ],

        'bannerTextLinkVariant' => [
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:palette.bannerTextLinkVariant',
            'showitem' => '
                text_link_text, target_footer_navigation, 
                    --linebreak--, 
                text_link_position,
            '
        ],

        'bannerButtonWidgetVariant' => [
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:palette.bannerButtonWidgetVariant',
            'showitem' => '
                button_widget_position, button_widget_text
            '
        ],

        'cookieInfoLabels' => [
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:palette.cookieInfoLabels',
            'showitem' => '
                cookie_name_text, cookie_lifetime_text,
                    --linebreak--, 
                cookie_provider_text, cookie_purpose_text,
                    --linebreak--, 
                cookie_description_text
            ',
        ],

        'cookieMoreInfoLabels' => [
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:palette.cookieMoreInfoLabels',
            'showitem' => '
                cookie_infos_show_text, cookie_infos_close_text,
            ',
        ],

        'linksBannerMenu' => [
            'showitem' => '
                banner_navigation
            ',
        ],



        'essential' => [
            'showitem' => '
                essential_title,
                    --linebreak--,
                essential_description,
            ',
        ],

        'essentialConsents' => [
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:palette.essentialOptIns',
            'showitem' => '
                essential_components    
            ',
        ],

        'otherGroup' => [
            'showitem' => '
                consent_other_groups
            ',
        ],

        'language' => [
            'showitem' => '
                sys_language_uid, l10n_parent
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
        'gtm_container_id' => [
            'exclude' => true,
            'label' => 'Google Tag Manager Container-ID',
            'description' => 'z. B. GTM-XXXXXX. Leer lassen, wenn kein GTM verwendet wird.',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'placeholder' => 'GTM-XXXXXX',
            ],
        ],
        'matomo_url' => [
            'exclude' => true,
            'label' => 'Matomo URL',
            'description' => 'Basis-URL der Matomo-Installation inkl. abschließendem /, z. B. https://matomo.example.com/',
            'config' => [
                'type' => 'input',
                'size' => 40,
                'eval' => 'trim',
                'placeholder' => 'https://matomo.example.com/',
            ],
        ],
        'matomo_site_id' => [
            'exclude' => true,
            'label' => 'Matomo Site-ID',
            'description' => 'Die idSite der Matomo-Property, z. B. 1',
            'config' => [
                'type' => 'input',
                'size' => 10,
                'eval' => 'trim',
                'placeholder' => '1',
            ],
        ],
        'matomo_mtm_url' => [
            'exclude' => true,
            'label' => 'Matomo Tag Manager Container-URL (optional)',
            'description' => 'Voll-URL der MTM-container.js. Wenn gesetzt, wird der MTM-Container statt des Standard-Trackers geladen.',
            'config' => [
                'type' => 'input',
                'size' => 40,
                'eval' => 'trim',
                'placeholder' => 'https://matomo.example.com/js/container_XXXX.js',
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
                'foreign_table' => 'tx_consentbanner_domain_model_banner',
                'foreign_table_where' => 'AND {#tx_consentbanner_domain_model_banner}.{#pid}=###CURRENT_PID### AND {#tx_consentbanner_domain_model_banner}.{#sys_language_uid} IN (-1,0)',
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
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.banner_active',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxLabeledToggle',
                'items' => [
                    [
                        'label' => '',
                        'invertStateDisplay' => true,
                        'labelChecked' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.banner_active.on',
                        'labelUnchecked' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.banner_active.off',
                    ]
                ],
                'eval' => 'maximumRecordsChecked',
                'validation' => [
                    'maximumRecordsChecked' => 2,
                    'maximumRecordsCheckedInPid' => 1
                ],
            ],
        ],

        'banner_id' => [
            'config' => [
                'type' => 'passthrough'
            ],
        ],

        'banner_hash' => [
            'config' => [
                'type' => 'passthrough'
            ],
        ],

        'banner_title' => [
            'exclude' => true,
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.banner_title',
            'config' => [
                'type' => 'input',
                'size' => 50,
                'eval' => 'trim',
                'required' => true
            ],
        ],


        'banner_description' => [
            'exclude' => true,
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.banner_description',
            'config' => [
                'type' => 'text',
                'eval' => 'trim',
                'cols' => 100,
                'rows' => 10
            ]
        ],

        'banner_layout' => [
            'exclude' => true,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.banner_layout',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.banner_layout.overlay', 'value' => 'cb-overlay'],
                    ['label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.banner_layout.overlayCompact', 'value' => 'cb-overlay-compact'],
                    ['label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.banner_layout.fullWidthBottom', 'value' => 'cb-bottom']
                ],
                'default' => 'cb-bottom',
            ],
        ],

        'banner_navigation' => [
            'exclude' => true,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.banner_navigation',
            'config' => [
                'type' => 'group',
                'allowed' => 'pages',
                'maxitems' => 4,
                'minitems' => 0,
                'size' => 2,
                'suggestOptions' => [
                    'default' => [
                        'additionalSearchFields' => 'title, nav_title, slug',
                        'addWhere' => 'AND pages.doktype = 1'
                    ]
                ],
                'fieldControl' => [
                    'elementBrowser' => [
                        'disabled' => false,
                    ],
                ],
            ]
        ],

        'lifetime_banner' => [
            'exclude' => true,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.lifetime_banner',
            'description' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.lifetime_banner.description',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'default' => 14,
                'items' => [
                    ['label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.lifetime_banner.l14days', 'value' => 14],
                    ['label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.lifetime_banner.l21days', 'value' => 21],
                    ['label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.lifetime_banner.l28days', 'value' => 28],
                ],
            ],
        ],

        'lifetime_user_consent' => [
            'exclude' => true,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.lifetime_user_consent',
            'description' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.lifetime_user_consent.description',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'default' => 1095,
                'items' => [
                    ['label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.lifetime_user_consent.l1year', 'value' => 365],
                    ['label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.lifetime_user_consent.l2years', 'value' => 730],
                    ['label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.lifetime_user_consent.l3years', 'value' => 1095],
                ],
            ],
        ],

        'accept_all_text' => [
            'exclude' => true,
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.accept_all_text',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],

        'confirm_selection_text' => [
            'exclude' => true,
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.confirm_selection_text',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],

        'save_and_close_text' => [
            'exclude' => true,
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.save_and_close_text',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],

        'advanced_settings_text' => [
            'exclude' => true,
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.advanced_settings_text',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],

        'accept_essential_text' => [
            'exclude' => true,
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.accept_essential_text',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],

        'cookie_infos_show_text' => [
            'exclude' => true,
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.cookie_infos_show_text',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],

        'cookie_infos_close_text' => [
            'exclude' => true,
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.cookie_infos_close_text',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],

        'cookie_name_text' => [
            'exclude' => true,
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.cookie_name_text',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],

        'cookie_lifetime_text' => [
            'exclude' => true,
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.cookie_lifetime_text',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],

        'cookie_provider_text' => [
            'exclude' => true,
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.cookie_provider_text',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],

        'cookie_purpose_text' => [
            'exclude' => true,
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.cookie_purpose_text',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],

        'cookie_description_text' => [
            'exclude' => true,
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.cookie_description_text',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],

        'essential_group_id' => [
            'config' => [
                'type' => 'passthrough'
            ],
        ],

        'essential_group_hash' => [
            'config' => [
                'type' => 'passthrough'
            ],
        ],

        'essential_title' => [
            'exclude' => true,
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.essential_title',
            'config' => [
                'type' => 'input',
                'size' => 50,
                'eval' => 'trim',
                'required' => true
            ],
        ],


        'essential_description' => [
            'exclude' => true,
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.essential_description',
            'config' => [
                'type' => 'text',
                'eval' => 'trim',
                'cols' => 100,
                'rows' => 10
            ]
        ],

        'essential_components' => [
            'exclude' => true,
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.essential_group_components',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_consentbanner_domain_model_consent_components',
                'foreign_field' => 'group_id',
                'foreign_match_fields' => [
                    'group_parent' => 'essential',
                ],
                'foreign_sortby' => 'sorting_foreign',
                'foreign_label' => 'component_title',
                'maxitems' => 10,
                'appearance' => [
                    'newRecordLinkTitle' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.essential_group_components.newRecordLinkTitle',
                    'useSortable' => true,
                    'levelLinksPosition' => 'top',
                    'enabledControls' => ['info' => false],
                    'collapseAll' => 1,
                    'expandSingle' => 1,
                ],
            ],
        ],

        'consent_other_groups' => [
            'exclude' => true,
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.consent_other_groups',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_consentbanner_domain_model_consent_groups',
                'foreign_field' => 'banner_id',
                'foreign_sortby' => 'sorting_foreign',
                'foreign_label' => 'group_title',
                'maxitems' => 5,
                'appearance' => [
                    'newRecordLinkTitle' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.consent_other_groups.newRecordLinkTitle',
                    'useSortable' => true,
                    'levelLinksPosition' => 'top',
                    'enabledControls' => ['info' => false],
                    'collapseAll' => 1,
                    'expandSingle' => 1,
                ],
            ],
        ],

        'privacy_settings_variant' => [
            'onChange' => 'reload',
            'exclude' => true,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.privacy_settings_variant',
            'description' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.privacy_settings_variant.description',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'default' => 10,
                'items' => [
                    ['label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.privacy_settings_variant.text_link', 'value' => 10],
                    ['label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.privacy_settings_variant.button_widget', 'value' => 20],
                ],
            ],
        ],

        'button_widget_position' => [
            'displayCond' => 'FIELD:privacy_settings_variant:=:20',
            'exclude' => true,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.button_widget_position',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'default' => 'left',
                'items' => [
                    ['label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.button_widget_position.left', 'value' => 'left'],
                    ['label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.button_widget_position.right', 'value' => 'right'],
                ],
            ],
        ],

        'button_widget_text' => [
            'displayCond' => 'FIELD:privacy_settings_variant:=:20',
            'exclude' => true,
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.button_widget_text',
            'description' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.button_widget_text.description',
            'config' => [
                'type' => 'input',
                'size' => 50,
                'eval' => 'trim',
            ],
        ],

        'text_link_position' => [
            'displayCond' => 'FIELD:privacy_settings_variant:=:10',
            'exclude' => true,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.text_link_position',
            'description' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.text_link_position.description',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'default' => 'last',
                'items' => [
                    ['label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.text_link_position.first', 'value' => 'first'],
                    ['label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.text_link_position.last', 'value' => 'last'],
                ],
            ],
        ],

        'text_link_text' => [
            'displayCond' => 'FIELD:privacy_settings_variant:=:10',
            'exclude' => true,
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.text_link_text',
            'description' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.text_link_text.description',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'default' => ''
            ],
        ],

        'target_footer_navigation' => [
            'displayCond' => 'FIELD:privacy_settings_variant:=:10',
            'exclude' => true,
            'l10n_mode' => 'exclude',
            'label' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.target_footer_navigation',
            'description' => 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:field.target_footer_navigation.description',
            'config' => [
                'type' => 'input',
                'size' => 50,
                'eval' => 'trim',
            ],
        ],

    ],
];

