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

namespace Connetation\T3wtkElements\Service;

use TYPO3\CMS\Core\Utility\GeneralUtility;

final class IconItemsProcFunc
{
    private const MANIFESTS = [
        'bootstrap-icons' => 'EXT:conn_t3wtk_elements/Resources/Private/IconManifests/bootstrap-icons.json',
        'font-awesome'    => 'EXT:conn_t3wtk_elements/Resources/Private/IconManifests/font-awesome.json',
        'ionicons'        => 'EXT:conn_t3wtk_elements/Resources/Private/IconManifests/ionicons.json',
    ];

    private const STYLE_ITEMS = [
        'font-awesome' => [
            ['label' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:text_icon.style.solid', 'value' => 'solid'],
            ['label' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:text_icon.style.regular', 'value' => 'regular'],
        ],
        'ionicons' => [
            ['label' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:text_icon.style.filled', 'value' => 'filled'],
            ['label' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:text_icon.style.outline', 'value' => 'outline'],
            ['label' => 'LLL:EXT:conn_t3wtk_elements/Resources/Private/Language/locallang_be.xlf:text_icon.style.sharp', 'value' => 'sharp'],
        ],
    ];

    public function getIconStyleItems(array &$parameters): void
    {
        $row              = $parameters['row'] ?? [];
        $libraryFieldName = (string)($parameters['config']['iconLibraryField'] ?? 'tx_conn_t3wtk_elements_text_icon_library');
        $library          = $this->firstValue($row[$libraryFieldName] ?? 'bootstrap-icons');

        $parameters['items'] = array_merge($parameters['items'] ?? [], self::STYLE_ITEMS[$library] ?? []);
    }

    public function getIconNameItems(array &$parameters): void
    {
        $row              = $parameters['row'] ?? [];
        $libraryFieldName = (string)($parameters['config']['iconLibraryField'] ?? 'tx_conn_t3wtk_elements_text_icon_library');
        $styleFieldName   = (string)($parameters['config']['iconStyleField'] ?? 'tx_conn_t3wtk_elements_text_icon_style');
        $library          = $this->firstValue($row[$libraryFieldName] ?? 'bootstrap-icons');
        $style            = $this->normalizeStyle($library, $this->firstValue($row[$styleFieldName] ?? 'solid'));

        $parameters['items'] = array_merge($parameters['items'] ?? [], $this->nameItemsFor($library, $style));
    }

    /**
     * @return array<int, array{label: string, value: string}>
     */
    public function nameItemsFor(string $library, string $style): array
    {
        $style = $this->normalizeStyle($library, $style);
        $items = [];

        foreach ($this->loadManifest($library) as $name => $info) {
            // json_decode(..., true) silently turns numeric-looking manifest
            // keys (e.g. bootstrap-icons' "123") into PHP ints — cast back so
            // consumers (incl. the JS icon picker) always see a string.
            $name = (string)$name;
            if (is_array($info)) {
                if (!in_array($style, $info['styles'] ?? [], true)) {
                    continue;
                }
                $items[] = ['label' => $info['label'] . ' (' . $name . ')', 'value' => $name];
            } else {
                $items[] = ['label' => $info . ' (' . $name . ')', 'value' => $name];
            }
        }

        usort($items, static fn (array $a, array $b): int => strcasecmp($a['label'], $b['label']));

        return $items;
    }

    /**
     * Switching "Icon library" (or "Icon style") can leave a previously
     * selected "Icon" value that doesn't exist for the new combination.
     */
    public function isValidName(string $library, string $style, string $name): bool
    {
        if ($name === '') {
            return false;
        }

        foreach ($this->nameItemsFor($library, $style) as $item) {
            if ($item['value'] === $name) {
                return true;
            }
        }

        return false;
    }

    public function firstAvailableName(string $library, string $style): string
    {
        $items = $this->nameItemsFor($library, $style);

        return $items[0]['value'] ?? '';
    }

    public function loadManifest(string &$library): array
    {
        if (!isset(self::MANIFESTS[$library])) {
            $library = 'bootstrap-icons';
        }

        $path = GeneralUtility::getFileAbsFileName(self::MANIFESTS[$library]);

        return is_string($path) && $path !== '' && is_file($path)
            ? json_decode((string)file_get_contents($path), true)
            : [];
    }

    /**
     * Switching "Icon library" doesn't reset an already-stored "Icon style"
     * value (e.g. Ionicons' "filled" left over after switching to Font
     * Awesome, which doesn't have that style) — fall back to that library's
     * first valid style instead of returning zero matching icons.
     */
    public function normalizeStyle(string $library, string $style): string
    {
        $validStyles = array_column(self::STYLE_ITEMS[$library] ?? [], 'value');

        if ($validStyles === [] || in_array($style, $validStyles, true)) {
            return $style;
        }

        return $validStyles[0];
    }

    private function firstValue(mixed $value): string
    {
        if (is_array($value)) {
            $value = $value[0] ?? '';
        }

        return (string)$value;
    }
}
