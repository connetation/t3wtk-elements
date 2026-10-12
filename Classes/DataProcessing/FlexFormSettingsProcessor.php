<?php

declare(strict_types=1);

/*
 * This file is part of the Connetation TYPO3 Web Toolkit - T3WTK.
 *
 * Copyright (c) Connetation Web Engineering GmbH
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

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
