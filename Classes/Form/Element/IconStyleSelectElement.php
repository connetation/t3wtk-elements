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

use Connetation\T3wtkElements\Service\IconItemsProcFunc;
use TYPO3\CMS\Backend\Form\Element\SelectSingleElement;
use TYPO3\CMS\Backend\Form\InlineStackProcessor;

final class IconStyleSelectElement extends SelectSingleElement
{
    public function __construct(
        InlineStackProcessor $inlineStackProcessor,
        private readonly IconItemsProcFunc $iconItemsProcFunc,
    ) {
        parent::__construct($inlineStackProcessor);
    }

    public function render(): array
    {
        $row     = $this->data['databaseRow'] ?? [];
        $library = $this->firstValue($row['tx_conn_t3wtk_elements_text_icon_library'] ?? 'bootstrap-icons');
        $style   = $this->firstValue($row['tx_conn_t3wtk_elements_text_icon_style'] ?? 'solid');
        $normalized = $this->iconItemsProcFunc->normalizeStyle($library, $style);

        if ($normalized !== $style) {
            $this->data['databaseRow']['tx_conn_t3wtk_elements_text_icon_style'] = $normalized;
            $this->data['parameterArray']['itemFormElValue'] = $normalized;
        }

        return parent::render();
    }

    private function firstValue(mixed $value): string
    {
        if (is_array($value)) {
            $value = $value[0] ?? '';
        }

        return (string)$value;
    }
}
