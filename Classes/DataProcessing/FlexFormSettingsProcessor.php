<?php

declare(strict_types=1);

namespace Connetation\T3wtkElements\DataProcessing;

use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

/**
 * Decodes a FlexForm settings column and merges it ON TOP of the content
 * object's TypoScript settings under the `settings` template variable.
 *
 * Use this instead of the core FlexFormProcessor with `as = settings`: that
 * would overwrite Fluid's reserved settings variable and blank all TypoScript
 * settings for the element (defaultHeaderType, media/lightbox config, …) —
 * same pitfall AbstractItemsProcessor::getTypoScriptSettings() guards against.
 *
 * Configuration:
 *   10 = Connetation\T3wtkElements\DataProcessing\FlexFormSettingsProcessor
 *   10 {
 *       fieldName = tx_conn_t3wtk_elements_example_settings
 *   }
 */
final class FlexFormSettingsProcessor implements DataProcessorInterface
{
    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData
    ): array {
        $fieldName = (string)$cObj->stdWrapValue('fieldName', $processorConfiguration, 'pi_flexform');
        $targetVar = (string)$cObj->stdWrapValue('as', $processorConfiguration, 'settings');

        $flexData = GeneralUtility::makeInstance(FlexFormService::class)
            ->convertFlexFormContentToArray((string)($processedData['data'][$fieldName] ?? ''));

        $typoScriptSettings = GeneralUtility::makeInstance(TypoScriptService::class)
            ->convertTypoScriptArrayToPlainArray($contentObjectConfiguration['settings.'] ?? []);

        $processedData[$targetVar] = array_replace_recursive(
            $typoScriptSettings,
            $flexData['settings'] ?? []
        );

        return $processedData;
    }
}
