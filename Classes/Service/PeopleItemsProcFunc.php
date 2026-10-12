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

final class PeopleItemsProcFunc
{
    public function getColumnsItems(array &$parameters): void
    {
        $style = $this->firstValue($parameters['row']['settings.style'] ?? '');

        if ($style !== 'visit-card') {
            return;
        }

        // $item is a plain array on older TYPO3 versions, or a
        // TYPO3\CMS\Core\Schema\Struct\SelectItem (TYPO3 v13+) — both support
        // ['value'] access (SelectItem implements ArrayAccess), so the
        // parameter is intentionally left untyped rather than `array`.
        $parameters['items'] = array_values(array_filter(
            $parameters['items'],
            static fn ($item): bool => (int)$item['value'] <= 2
        ));
    }

    private function firstValue(mixed $value): string
    {
        if (is_array($value)) {
            $value = $value[0] ?? '';
        }

        return (string)$value;
    }
}
