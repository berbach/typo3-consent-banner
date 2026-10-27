<?php

use TYPO3\CMS\Core\Information\Typo3Version;

$ll = 'LLL:EXT:consent_banner/Resources/Private/Language/locallang_mod.xlf:';
$llCore = 'LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:';

// TYPO3 14 erwartet valuePicker-Items als ['label' => …, 'value' => …] (das alte Format ist deprecated),
// TYPO3 13 liest sie nur positional als [label, value].
$cookieTypes = ['Cookie', 'Session-Cookie', 'LocalStorage', 'SessionStorage', 'IndexedDB'];
$cookieTypeItems = (new Typo3Version())->getMajorVersion() >= 14
    ? array_map(static fn (string $type): array => ['label' => $type, 'value' => $type], $cookieTypes)
    : array_map(static fn (string $type): array => [$type, $type], $cookieTypes);

return [
    'ctrl' => [
        'title' => 'Cookie',
        'label' => 'cookie_name',
        'sortby' => 'sorting_foreign',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'delete' => 'deleted',
        'adminOnly' => true,
        'rootLevel' => -1,
        'hideTable' => true,
        'versioningWS' => false,
        'hideAtCopy' => true,
        'typeicon_classes' => [
            'default' => 'module-cookie',
        ],
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
        'transOrigPointerField' => 'l10n_parent',
        'languageField' => 'sys_language_uid',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'translationSource' => 'l10n_source',
        'security' => [
            'ignorePageTypeRestriction' => true,
        ],
    ],
    'types' => [
        '0' => [
            'showitem' => '
                cookie_name, cookie_type,
                --linebreak--,
                cookie_purpose,
                --linebreak--,
                cookie_lifetime,
                --linebreak--,
                cookie_description,
                --div--;' . $llCore . 'language,
                    sys_language_uid, l10n_parent,
                --div--;' . $llCore . 'access,
                    hidden,
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
                'type' => 'group',
                'allowed' => 'tx_consentbanner_domain_model_cookie',
                'size' => 1,
                'maxitems' => 1,
                'minitems' => 0,
                'default' => 0,
            ],
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'hidden' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
            ],
        ],
        'component' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'cookie_name' => [
            'exclude' => true,
            'label' => $ll . 'field.cookie_name',
            'config' => [
                'type' => 'input',
                'size' => 50,
                'eval' => 'trim',
                'required' => true,
            ],
        ],
        'cookie_type' => [
            'exclude' => true,
            'label' => $ll . 'field.cookie_type',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'valuePicker' => [
                    'items' => $cookieTypeItems,
                ],
            ],
        ],
        'cookie_description' => [
            'exclude' => true,
            'label' => $ll . 'field.cookie_description',
            'config' => [
                'type' => 'text',
                'eval' => 'trim',
                'cols' => 50,
                'rows' => 5,
            ],
        ],
        'cookie_purpose' => [
            'exclude' => true,
            'label' => $ll . 'field.cookie_purpose',
            'config' => [
                'type' => 'text',
                'eval' => 'trim',
                'cols' => 50,
                'rows' => 5,
            ],
        ],
        'cookie_lifetime' => [
            'exclude' => true,
            'label' => $ll . 'field.cookie_lifetime',
            'config' => [
                'type' => 'input',
                'size' => 50,
                'eval' => 'trim',
            ],
        ],
    ],
];
