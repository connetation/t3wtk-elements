<?php

declare(strict_types=1);

namespace Connetation\T3kitElements\DataProcessing;

use Connetation\T3kitBase\DataProcessing\AbstractItemsProcessor;

final class LightboxGalleryProcessor extends AbstractItemsProcessor
{
    protected function getTable(): string
    {
        return 'tx_conn_t3kit_elements_domain_model_gallery_item';
    }

    protected function getDefaultTargetVariable(): string
    {
        return 'items';
    }

    protected function getSettingsField(array $processorConfiguration): string
    {
        return 'tx_conn_t3kit_elements_lightbox_gallery_settings';
    }

    protected function getFalFields(): array
    {
        return ['image' => 'imageFile'];
    }

    protected function resolveSettings(array $raw, string $settingsField): array
    {
        $columns = (int)($raw['columns'] ?? 3);
        $colMap  = [
            2 => 'col-sm-6',
            3 => 'col-sm-6 col-lg-4',
            4 => 'col-sm-6 col-xl-3',
            6 => 'col-4 col-sm-3 col-md-2',
        ];

        $gapMap = [
            'none'   => 'g-0',
            'small'  => 'g-2',
            'medium' => 'g-3',
            'large'  => 'g-4',
        ];

        $allowedPositions = ['bottom', 'center', 'top'];
        $allowedAligns    = ['left', 'center', 'right'];
        $allowedFits      = ['cover', 'contain'];
        $allowedGaps      = ['none', 'small', 'medium', 'large'];
        $allowedBorders   = ['none', 'solid', 'dashed', 'dotted'];
        $allowedRadii     = ['none', 'small', 'medium', 'large', 'pill'];

        $textPosition = in_array($raw['textPosition'] ?? '', $allowedPositions, true)
            ? $raw['textPosition']
            : 'bottom';
        $textAlign = in_array($raw['textAlign'] ?? '', $allowedAligns, true)
            ? $raw['textAlign']
            : 'left';
        $fit = in_array($raw['fit'] ?? '', $allowedFits, true)
            ? $raw['fit']
            : 'cover';
        $gap = in_array($raw['gap'] ?? '', $allowedGaps, true)
            ? $raw['gap']
            : 'medium';
        $borderStyle = in_array($raw['borderStyle'] ?? '', $allowedBorders, true)
            ? $raw['borderStyle']
            : 'dashed';
        $borderRadius = in_array($raw['borderRadius'] ?? '', $allowedRadii, true)
            ? $raw['borderRadius']
            : 'medium';

        // Native color-with-opacity FlexForm fields; empty just means "use
        // the theme default", so this only guards against garbage — no
        // forced fallback hex like AbstractItemsProcessor::sanitizeHexColor.
        $borderColor     = $this->sanitizeOptionalHexColor($raw['borderColor'] ?? '');
        $backgroundColor = $this->sanitizeOptionalHexColor($raw['backgroundColor'] ?? '');

        return [
            'columns'                => $columns,
            'colClass'               => $colMap[$columns] ?? $colMap[3],
            'gapClass'               => $gapMap[$gap] ?? $gapMap['medium'],
            'textPosition'           => $textPosition,
            'textAlign'              => $textAlign,
            'fit'                    => $fit,
            'borderStyle'            => $borderStyle,
            'borderColor'            => $borderColor,
            'borderRadius'           => $borderRadius,
            'backgroundColor'        => $backgroundColor,
            'captionInLightboxOnly'  => (bool)($raw['captionInLightboxOnly'] ?? false),
        ];
    }

    protected function defaultSettings(string $settingsField): array
    {
        return [
            'columns'                => 3,
            'colClass'               => 'col-sm-6 col-lg-4',
            'gapClass'               => 'g-3',
            'textPosition'           => 'bottom',
            'textAlign'              => 'left',
            'fit'                    => 'cover',
            'borderStyle'            => 'dashed',
            'borderColor'            => '',
            'borderRadius'           => 'medium',
            'backgroundColor'        => '',
            'captionInLightboxOnly'  => false,
        ];
    }

    private function sanitizeOptionalHexColor(string $value): string
    {
        $value = trim($value);
        return preg_match('/^#[0-9a-fA-F]{3,8}$/', $value) ? $value : '';
    }
}
