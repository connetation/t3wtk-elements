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

namespace Connetation\T3wtkElements\Controller\Backend;

use Connetation\T3wtkElements\Service\IconItemsProcFunc;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\JsonResponse;

final class IconListAjaxController
{
    public function __construct(
        private readonly IconItemsProcFunc $iconItemsProcFunc,
    ) {}

    public function listAction(ServerRequestInterface $request): ResponseInterface
    {
        $params  = $request->getQueryParams();
        $library = (string)($params['library'] ?? 'bootstrap-icons');
        $style   = (string)($params['style'] ?? 'solid');

        $data  = $this->iconItemsProcFunc->loadManifest($library);
        $style = $this->iconItemsProcFunc->normalizeStyle($library, $style);

        $items = [];

        foreach ($data as $name => $info) {
            // json_decode(..., true) silently turns numeric-looking manifest
            // keys (e.g. bootstrap-icons' "123") into PHP ints — cast back so
            // the JSON response always sends a string, not a number.
            $name = (string)$name;
            if (is_array($info)) {
                if (!in_array($style, $info['styles'] ?? [], true)) {
                    continue;
                }
                $items[] = ['name' => $name, 'label' => $info['label']];
            } else {
                $items[] = ['name' => $name, 'label' => $info];
            }
        }

        usort($items, static fn (array $a, array $b): int => strcasecmp($a['label'], $b['label']));

        return new JsonResponse($items);
    }
}
