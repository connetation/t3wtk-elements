<?php

declare(strict_types=1);

/*
 * This file is part of the Connetation TYPO3 Web Toolkit - T3WTK.
 *
 * Copyright (c) Connetation Web Engineering GmbH
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
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

use Connetation\T3wtkBase\Utility\ResponsiveValue;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

/**
 * Resolves one per-device setting (renderType connResponsiveRange,
 * "desktop,tablet,mobile") for elements whose settings come from a generic
 * processor (FlexFormSettingsProcessor & co.). Runs after it and replaces
 * settings.<field> with the desktop value, adding <field>Tablet and
 * <field>Mobile (empty = same as the next larger device).
 *
 *   20 = Connetation\T3wtkElements\DataProcessing\ResponsiveSettingsProcessor
 *   20 {
 *       field = height
 *       min = 20
 *       max = 100
 *       default = 70
 *       # former select values of this field
 *       legacy = medium:55,large:70,fullscreen:100
 *   }
 */
final class ResponsiveSettingsProcessor implements DataProcessorInterface
{
    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData
    ): array {
        $targetVar = (string)$cObj->stdWrapValue('as', $processorConfiguration, 'settings');
        $field     = (string)$cObj->stdWrapValue('field', $processorConfiguration, '');
        if ($field === '' || !\is_array($processedData[$targetVar] ?? null)) {
            return $processedData;
        }

        $legacy = [];
        foreach (array_filter(explode(',', (string)$cObj->stdWrapValue('legacy', $processorConfiguration, ''))) as $pair) {
            [$keyword, $value] = array_map('trim', explode(':', $pair, 2)) + [1 => ''];
            if ($keyword !== '' && is_numeric($value)) {
                $legacy[$keyword] = (int)$value;
            }
        }

        $values = ResponsiveValue::inherited(ResponsiveValue::parse(
            $processedData[$targetVar][$field] ?? null,
            (int)$cObj->stdWrapValue('min', $processorConfiguration, 0),
            (int)$cObj->stdWrapValue('max', $processorConfiguration, PHP_INT_MAX),
            (int)$cObj->stdWrapValue('default', $processorConfiguration, 0),
            $legacy
        ));

        $processedData[$targetVar][$field]            = $values['desktop'];
        $processedData[$targetVar][$field . 'Tablet'] = $values['tablet'];
        $processedData[$targetVar][$field . 'Mobile'] = $values['mobile'];

        return $processedData;
    }
}
