<?php

declare(strict_types=1);

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
