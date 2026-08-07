<?php
declare(strict_types=1);

defined('TYPO3') || die('Access denied.');

use Bb\ConsentBanner\Hook\DataHandlerHook;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Imaging\IconRegistry;
use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

call_user_func(static function () {

    // Add module configuration
    ExtensionManagementUtility::addTypoScriptSetup(
        'module.tx_consent_banner {
            settings {
                storagePid = 999
            }
            view {
                templateRootPaths.0 = EXT:consent_banner/Resources/Private/Backend/Templates/
                partialRootPaths.0 = EXT:consent_banner/Resources/Private/Backend/Partials/
                layoutRootPaths.0 = EXT:consent_banner/Resources/Private/Backend/Layouts/
            }
        }'
    );

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass']['banner']
        = DataHandlerHook::class;
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass']['banner']
        = DataHandlerHook::class;

    // Custom FormEngine element for the service autocomplete field.
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1721830001] = [
        'nodeName' => 'serviceAutocomplete',
        'priority' => 40,
        'class' => \Bb\ConsentBanner\Form\Element\ServiceAutocompleteElement::class,
    ];

    // Load the service autocomplete JS/CSS on every backend page so the field
    // also initializes when it is rendered as a lazily loaded nested inline
    // record (Banner -> group -> component), where the per-element module
    // import is not reliably executed.
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_pagerenderer.php']['render-preProcess'][]
        = \Bb\ConsentBanner\Hook\BackendAssetHook::class . '->registerAssets';
});