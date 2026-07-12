<?php

declare(strict_types=1);

namespace Connetation\Elements\Controller\Backend;

use Connetation\Elements\Service\IconItemsProcFunc;
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
