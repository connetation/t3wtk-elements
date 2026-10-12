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

namespace Connetation\T3wtkElements\Form\Element;

use Connetation\T3wtkBase\Utility\Devices;
use TYPO3\CMS\Backend\Form\Element\AbstractFormElement;
use TYPO3\CMS\Core\Imaging\IconFactory;
use TYPO3\CMS\Core\Imaging\IconSize;
use TYPO3\CMS\Core\Page\JavaScriptModuleInstruction;
use TYPO3\CMS\Core\Utility\StringUtility;

/**
 * One slider with Desktop / Tablet / Mobile device tabs (renderType
 * "connResponsiveRange") — per-device values without three separate fields.
 *
 * The field stores "desktop,tablet,mobile" (e.g. "70,,45"); an empty
 * tablet/mobile value inherits from the next larger device, and a plain
 * single value (e.g. "70", as saved by a former number field) is desktop
 * only — so switching an existing field to this renderType needs no data
 * migration. Frontend parsing: SlideItemsProcessor::resolveHeroHeights().
 *
 * Device names and breakpoints (shown on the tabs and below them) come from
 * Devices — the single source for every per-device setting.
 *
 * Field config (type "input"):
 *   <renderType>connResponsiveRange</renderType>
 *   <responsive><lower>20</lower><upper>100</upper><step>5</step><unit>vh</unit></responsive>
 *   <default>50</default>
 *
 * <responsive><auto>1</auto></responsive> — an empty tablet/mobile value is
 * shown as "Auto" (the element decides, e.g. a fluid grid) instead of
 * "Same as Desktop/Tablet".
 */
final class ResponsiveRangeElement extends AbstractFormElement
{
    private const DEVICES = [
        'desktop' => 'actions-device-desktop',
        'tablet'  => 'actions-device-tablet',
        'mobile'  => 'actions-device-mobile',
    ];

    // Device labels live in conn_t3wtk_base (shared with the site setting type connResponsive).
    private const LLL = 'LLL:EXT:conn_t3wtk_base/Resources/Private/Language/locallang_be.xlf:responsive_range.';

    public function __construct(
        private readonly IconFactory $iconFactory,
    ) {}

    public function render(): array
    {
        $resultArray    = $this->initializeResultArray();
        $parameterArray = $this->data['parameterArray'];
        $config         = $parameterArray['fieldConf']['config'] ?? [];
        $responsive     = $config['responsive'] ?? [];

        $lower   = (int)($responsive['lower'] ?? 0);
        $upper   = (int)($responsive['upper'] ?? 100);
        $step    = max(1, (int)($responsive['step'] ?? 1));
        $unit    = (string)($responsive['unit'] ?? '');
        $default = (string)($config['default'] ?? $lower);
        $auto    = (bool)($responsive['auto'] ?? false);
        $value   = (string)($parameterArray['itemFormElValue'] ?? '');
        $fieldId = StringUtility::getUniqueId('formengine-responsive-range-');
        $lang    = $this->getLanguageService();

        $fieldInformationResult = $this->renderFieldInformation();
        $resultArray = $this->mergeChildReturnIntoExistingResult($resultArray, $fieldInformationResult, false);

        // Device names + screen-width ranges, from Devices (the single source).
        $tabs = '';
        $rangeAttributes = '';
        foreach (self::DEVICES as $device => $iconIdentifier) {
            $range = $this->rangeLabel($device);
            $label = htmlspecialchars($lang->sL(self::LLL . $device));
            $rangeAttributes .= ' data-range-' . $device . '="' . htmlspecialchars($lang->sL(self::LLL . $device) . ': ' . $range) . '"';
            $tabs .= '<button type="button" class="btn btn-default btn-sm conn-responsive-range__tab"'
                . ' data-device="' . $device . '" role="tab" aria-selected="false"'
                . ' title="' . $label . ': ' . htmlspecialchars($range) . '">'
                . $this->iconFactory->getIcon($iconIdentifier, IconSize::SMALL)->render()
                . '<span class="conn-responsive-range__tab-label">' . $label . '</span>'
                . '<span class="conn-responsive-range__badge"></span>'
                . '</button>';
        }

        $html = [];
        // Not a single input — fieldset + legend (wrapWithFieldsetAndLegend) instead of <label for>.
        $html[] = '<div class="formengine-field-item t3js-formengine-field-item">';
        $html[] =   $fieldInformationResult['html'];
        $html[] =   '<div class="form-control-wrap conn-responsive-range" id="' . htmlspecialchars($fieldId) . '"'
            . ' data-default="' . htmlspecialchars($default) . '"'
            . ' data-unit="' . htmlspecialchars($unit) . '"'
            . $rangeAttributes
            . ' data-auto="' . ($auto ? '1' : '0') . '"'
            . ' data-label-auto="' . htmlspecialchars($lang->sL(self::LLL . 'auto')) . '"'
            . ' data-label-inherit-auto="' . htmlspecialchars($lang->sL(self::LLL . 'inherit.auto')) . '"'
            . ' data-label-inherit-desktop="' . htmlspecialchars($lang->sL(self::LLL . 'inherit.desktop')) . '"'
            . ' data-label-inherit-tablet="' . htmlspecialchars($lang->sL(self::LLL . 'inherit.tablet')) . '">';
        $html[] =     '<input type="hidden" class="conn-responsive-range__value" name="' . htmlspecialchars($parameterArray['itemFormElName']) . '" value="' . htmlspecialchars($value) . '">';
        $html[] =     '<div class="btn-group conn-responsive-range__tabs" role="tablist">' . $tabs . '</div>';
        $html[] =     '<p class="conn-responsive-range__device"></p>';
        $html[] =     '<div class="conn-responsive-range__controls">';
        $html[] =       '<input type="range" class="form-range conn-responsive-range__slider" min="' . $lower . '" max="' . $upper . '" step="' . $step . '">';
        $html[] =       '<div class="input-group input-group-sm conn-responsive-range__number">';
        $html[] =         '<input type="number" class="form-control" min="' . $lower . '" max="' . $upper . '" step="' . $step . '">';
        $html[] =         $unit !== '' ? '<span class="input-group-text">' . htmlspecialchars($unit) . '</span>' : '';
        $html[] =       '</div>';
        $html[] =     '</div>';
        $html[] =     '<div class="conn-responsive-range__meta">';
        $html[] =       '<span class="conn-responsive-range__hint"></span>';
        $html[] =       '<button type="button" class="btn btn-link btn-sm conn-responsive-range__reset" hidden>'
            . htmlspecialchars($lang->sL(self::LLL . 'reset')) . '</button>';
        $html[] =     '</div>';
        $html[] =   '</div>';
        $html[] = '</div>';

        $resultArray['html'] = $this->wrapWithFieldsetAndLegend(implode(LF, $html));
        $resultArray['stylesheetFiles'][] = 'EXT:conn_t3wtk_elements/Resources/Public/Css/responsive-range.css';
        $resultArray['javaScriptModules'][] = JavaScriptModuleInstruction::create('@connetation/t3wtk-elements/responsive-range.js')
            ->instance($fieldId);

        return $resultArray;
    }

    /** "1200 px and wider" / "768–1199 px" / "below 768 px" (screen width). */
    private function rangeLabel(string $device): string
    {
        $range = Devices::range($device);
        $lang  = $this->getLanguageService();

        if ($range['max'] === null) {
            return sprintf($lang->sL(self::LLL . 'range.from'), $range['min']);
        }
        if ($range['min'] === null) {
            return sprintf($lang->sL(self::LLL . 'range.below'), $range['max'] + 1);
        }

        return sprintf($lang->sL(self::LLL . 'range.between'), $range['min'], $range['max']);
    }
}
