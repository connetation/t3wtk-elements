<?php

declare(strict_types=1);

namespace Connetation\T3kitElements\DataProcessing;

use Connetation\T3kitBase\DataProcessing\AbstractItemsProcessor;

final class FlipCardItemsProcessor extends AbstractItemsProcessor
{
    private const COL_MAP = [
        1 => 'col-12',
        2 => 'col-sm-6',
        3 => 'col-sm-6 col-lg-4',
        4 => 'col-sm-6 col-xl-3',
    ];

    private const COLOR_VARS = [
        'bg_color'       => '--conn-flip-card-bg',
        'title_color'    => '--conn-flip-card-title',
        'subtitle_color' => '--conn-flip-card-subtitle',
        'text_color'     => '--conn-flip-card-text',
    ];

    protected function getTable(): string
    {
        return 'tx_conn_t3kit_elements_domain_model_flip_card_item';
    }

    protected function getDefaultTargetVariable(): string
    {
        return 'cards';
    }

    protected function getSettingsField(array $processorConfiguration): string
    {
        return 'tx_conn_t3kit_elements_flip_card_settings';
    }

    protected function getFalFields(): array
    {
        return ['image' => 'imageFile', 'back_image' => 'backImageFile'];
    }

    protected function resolveSettings(array $raw, string $settingsField): array
    {
        $columns = (int)($raw['columns'] ?? 3);

        return [
            'columns'   => $columns,
            'colClass'  => self::COL_MAP[$columns] ?? self::COL_MAP[3],
            'ratio'     => $this->resolveChoice($raw['ratio'] ?? null, ['portrait', 'square', 'landscape']),
            'position'  => $this->resolveChoice($raw['contentPosition'] ?? null, ['bottom', 'center', 'top']),
            'direction' => $this->resolveChoice($raw['direction'] ?? null, ['horizontal', 'vertical']),
            'trigger'   => $this->resolveChoice($raw['trigger'] ?? null, ['hover', 'click']),
            'style'     => $this->resolveChoice($raw['style'] ?? null, ['default', 'shadow', 'flat']),
        ];
    }

    protected function defaultSettings(string $settingsField): array
    {
        return [
            'columns'   => 3,
            'colClass'  => self::COL_MAP[3],
            'ratio'     => 'portrait',
            'position'  => 'bottom',
            'direction' => 'horizontal',
            'trigger'   => 'hover',
            'style'     => 'default',
        ];
    }

    // Front and back share the same field set (the back one "back_"-prefixed,
    // plus the link only the back has). Records saved before an *_align column
    // existed hold '' rather than the TCA default, which would render a broken
    // "text-" class.
    protected function postProcessRows(array $rows, array $settings): array
    {
        $alignFields = ['title_align', 'subtitle_align', 'back_title_align', 'back_subtitle_align', 'back_link_align'];
        foreach ($rows as &$row) {
            foreach ($alignFields as $field) {
                $row[$field] = $this->resolveChoice($row[$field] ?? null, ['center', 'start', 'end']);
            }
            $row['faceStyle']     = $this->buildFaceStyle($row, '');
            $row['backFaceStyle'] = $this->buildFaceStyle($row, 'back_');
        }
        unset($row);

        return $rows;
    }

    /**
     * Per-face color overrides as CSS custom properties (see _flip-card.scss);
     * unset or invalid colors are left out so the built-in look applies.
     */
    private function buildFaceStyle(array $row, string $prefix): string
    {
        $style = '';
        foreach (self::COLOR_VARS as $field => $cssVar) {
            $color = $this->sanitizeHexColor((string)($row[$prefix . $field] ?? ''), '');
            if ($color !== '') {
                $style .= $cssVar . ': ' . $color . ';';
            }
        }

        return $style;
    }

    /** First entry of $allowed is the default. */
    private function resolveChoice(?string $value, array $allowed): string
    {
        return in_array($value, $allowed, true) ? $value : $allowed[0];
    }
}
