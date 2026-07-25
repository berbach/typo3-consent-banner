<?php

declare(strict_types=1);

namespace Bb\ConsentBanner\Form\Element;

use TYPO3\CMS\Backend\Form\Element\AbstractFormElement;
use TYPO3\CMS\Backend\Routing\UriBuilder;
use TYPO3\CMS\Core\Page\JavaScriptModuleInstruction;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Core\Utility\StringUtility;

/**
 * FormEngine element "serviceAutocomplete".
 *
 * Renders a standard TYPO3 backend input field (identical markup/classes to the
 * native input element, so it blends into the current backend theme). While
 * typing, the accompanying JavaScript module queries the bundled service
 * database via a backend AJAX route and shows a suggestion list directly below
 * the field (styled like the native suggest wizard).
 *
 * Selecting a suggestion writes the service_name into this very field (which is
 * persisted like any other field), pre-fills the sibling service_* fields and
 * creates/fills the inline cookie rows — all client-side and fully editable.
 *
 * The element itself only prepares markup + data attributes; all interaction
 * lives in @bb/consentbanner/ServiceAutocomplete.js. The currently edited
 * record language (2-letter code) is resolved here and handed to the JS so it
 * can pick the matching localized text (de/en/…) from the JSON.
 */
class ServiceAutocompleteElement extends AbstractFormElement
{
    protected $defaultFieldInformation = [
        'tcaDescription' => [
            'renderType' => 'tcaDescription',
        ],
    ];

    public function render(): array
    {
        $parameterArray = $this->data['parameterArray'];
        $resultArray = $this->initializeResultArray();
        $config = $parameterArray['fieldConf']['config'];

        $itemValue = $parameterArray['itemFormElValue'];
        $itemName = (string)$parameterArray['itemFormElName'];
        $fieldId = StringUtility::getUniqueId('formengine-service-autocomplete-');
        $width = $this->formMaxWidth(
            MathUtility::forceIntegerInRange($config['size'] ?? $this->defaultInputWidth, $this->minimumInputWidth, $this->maxInputWidth)
        );

        $renderedLabel = $this->renderLabel($fieldId);

        $fieldInformationResult = $this->renderFieldInformation();
        $fieldInformationHtml = $fieldInformationResult['html'];
        $resultArray = $this->mergeChildReturnIntoExistingResult($resultArray, $fieldInformationResult, false);

        $evalList = GeneralUtility::trimExplode(',', $config['eval'] ?? '', true);
        $formEngineInputParams = ['field' => $itemName];
        if ($evalList !== []) {
            $formEngineInputParams['evalList'] = implode(',', $evalList);
        }

        // Data the JS module needs. Sibling field names are derived on the
        // client by swapping the trailing "[service_name]" segment.
        $serviceConfig = [
            'ajaxUrl' => (string)$this->getAjaxUrl(),
            'language' => $this->resolveRecordLanguageCode(),
            'fieldName' => $itemName,
            'inlineField' => 'service_cookies',
            // TCA field name -> JSON service key
            'serviceFields' => [
                'service_provider' => 'service_provider',
                'service_privacy_link' => 'service_privacy_link',
                'service_publisher' => 'service_publisher',
                'service_data_collected' => 'service_data_collected',
            ],
            // inline cookie TCA field -> JSON cookie key
            'cookieFields' => [
                'cookie_name' => 'name',
                'cookie_type' => 'type',
                'cookie_lifetime' => 'lifetime',
                'cookie_purpose' => 'purpose',
                'cookie_description' => 'description',
            ],
        ];

        $attributes = [
            'type' => 'text',
            'value' => '',
            'id' => $fieldId,
            'class' => implode(' ', [
                'form-control',
                'form-control-clearable',
                't3js-clearable',
                't3js-cb-service-autocomplete',
            ]),
            'data-formengine-validation-rules' => $this->getValidationDataAsJsonString($config),
            'data-formengine-input-params' => (string)json_encode($formEngineInputParams, JSON_THROW_ON_ERROR),
            'data-formengine-input-name' => $itemName,
            'data-cb-service-config' => (string)json_encode($serviceConfig, JSON_THROW_ON_ERROR),
            'autocomplete' => 'off',
        ];

        $mainFieldHtml = [];
        $mainFieldHtml[] = '<div class="form-control-wrap" style="max-width: ' . $width . 'px">';
        $mainFieldHtml[] =  '<div class="form-wizards-wrap">';
        $mainFieldHtml[] =      '<div class="form-wizards-item-element">';
        $mainFieldHtml[] =          '<div class="t3js-cb-service-autocomplete-wrap" style="position: relative;">';
        $mainFieldHtml[] =              '<input ' . GeneralUtility::implodeAttributes($attributes, true) . ' />';
        $mainFieldHtml[] =              '<input type="hidden" name="' . htmlspecialchars($itemName) . '" value="' . htmlspecialchars((string)$itemValue) . '" />';
        $mainFieldHtml[] =              '<div class="t3js-cb-service-suggest cb-service-suggest" hidden></div>';
        $mainFieldHtml[] =          '</div>';
        $mainFieldHtml[] =      '</div>';
        $mainFieldHtml[] =  '</div>';
        $mainFieldHtml[] = '</div>';
        $mainFieldHtml = implode(LF, $mainFieldHtml);

        $resultArray['html'] = $renderedLabel . '
            <div class="formengine-field-item t3js-formengine-field-item">
                ' . $fieldInformationHtml . $mainFieldHtml . '
            </div>';

        $resultArray['javaScriptModules'][] = JavaScriptModuleInstruction::create(
            '@bb/consentbanner/ServiceAutocomplete.js'
        );
        $resultArray['stylesheetFiles'][] = 'EXT:consent_banner/Resources/Public/Css/Backend.css';

        return $resultArray;
    }

    /**
     * Build the tokenized backend AJAX url for the service search route.
     */
    protected function getAjaxUrl(): string
    {
        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);
        // AJAX routes from Configuration/Backend/AjaxRoutes.php are registered
        // with an "ajax_" prefix on their identifier.
        return (string)$uriBuilder->buildUriFromRoute('ajax_consentbanner_service_search');
    }

    /**
     * Resolve the 2-letter language code of the currently edited record so the
     * JS module can pick the matching localized text from the JSON.
     *
     * The record's sys_language_uid is resolved against the site's configured
     * languages via the real locale (Locale::getLanguageCode()). We deliberately
     * do NOT use FormEngine's systemLanguageRows "iso": for the default language
     * it carries the placeholder "DEF" instead of the actual code, which cannot
     * be mapped back to de/en. Returns a lowercase 2-letter code (e.g. "de",
     * "en"); empty string when it cannot be resolved, in which case the JS
     * applies its own en→de fallback.
     */
    protected function resolveRecordLanguageCode(): string
    {
        $languageUid = $this->data['databaseRow']['sys_language_uid'] ?? 0;
        if (is_array($languageUid)) {
            $languageUid = (int)($languageUid[0] ?? 0);
        }
        $languageUid = (int)$languageUid;

        $pid = (int)($this->data['effectivePid'] ?? ($this->data['databaseRow']['pid'] ?? 0));

        try {
            $site = GeneralUtility::makeInstance(SiteFinder::class)->getSiteByPageId($pid);
            // Language uid -1 ("all languages") falls back to the default language.
            $language = $languageUid < 0 ? $site->getDefaultLanguage() : $site->getLanguageById($languageUid);
            $code = strtolower($language->getLocale()->getLanguageCode());
            if (preg_match('/^[a-z]{2}$/', $code)) {
                return $code;
            }
        } catch (\Throwable) {
            // No resolvable site/language — let the JS fallback (en→de) decide.
        }

        return '';
    }
}
