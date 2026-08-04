<?php

declare(strict_types=1);

namespace Connetation\Elements\Service;

final class PeopleItemsProcFunc
{
    public function getColumnsItems(array &$parameters): void
    {
        $style = $this->firstValue($parameters['row']['settings.style'] ?? '');

        if ($style !== 'visit-card') {
            return;
        }

        $parameters['items'] = array_values(array_filter(
            $parameters['items'],
            static fn (array $item): bool => (int)$item['value'] <= 2
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
