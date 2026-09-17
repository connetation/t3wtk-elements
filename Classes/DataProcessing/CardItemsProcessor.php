<?php

declare(strict_types=1);

namespace Connetation\Elements\DataProcessing;

use Connetation\Base\DataProcessing\AbstractItemsProcessor;

final class CardItemsProcessor extends AbstractItemsProcessor
{
    protected function getTable(): string
    {
        return 'tx_conn_elements_domain_model_card_item';
    }

    protected function getDefaultTargetVariable(): string
    {
        return 'cards';
    }

    protected function getSettingsField(array $processorConfiguration): string
    {
        return 'tx_conn_elements_card_group_settings';
    }

    protected function getFalFields(): array
    {
        return ['image' => 'imageFile'];
    }

    protected function resolveSettings(array $raw, string $settingsField): array
    {
        $columns = (int)($raw['columns'] ?? 3);
        $colMap  = [
            1 => 'col-12',
            2 => 'col-sm-6',
            3 => 'col-sm-6 col-lg-4',
            4 => 'col-sm-6 col-xl-3',
        ];
        $style = in_array($raw['style'] ?? 'default', ['default', 'shadow', 'flat'], true)
            ? ($raw['style'] ?? 'default')
            : 'default';
        $bodyPosition = in_array($raw['bodyPosition'] ?? 'top', ['top', 'center', 'bottom'], true)
            ? ($raw['bodyPosition'] ?? 'top')
            : 'top';

        return [
            'columns'      => $columns,
            'colClass'     => $colMap[$columns] ?? $colMap[3],
            'style'        => $style,
            'accentBar'    => (bool)($raw['accentBar'] ?? true),
            'bodyPosition' => $bodyPosition,
        ];
    }

    protected function defaultSettings(string $settingsField): array
    {
        return [
            'columns'      => 3,
            'colClass'     => 'col-sm-6 col-lg-4',
            'style'        => 'default',
            'accentBar'    => true,
            'bodyPosition' => 'top',
        ];
    }

    // Records saved before the *_align columns existed have '' rather than
    // the TCA default (only applied to newly created records), which would
    // render a broken "text-" class.
    protected function postProcessRows(array $rows, array $settings): array
    {
        foreach ($rows as &$row) {
            $row['title_align']    = $this->resolveAlign($row['title_align'] ?? null);
            $row['subtitle_align'] = $this->resolveAlign($row['subtitle_align'] ?? null);
            $row['link_align']     = $this->resolveAlign($row['link_align'] ?? null);
        }
        unset($row);

        return $rows;
    }

    private function resolveAlign(?string $align): string
    {
        return in_array($align, ['start', 'center', 'end'], true) ? $align : 'start';
    }
}
