<?php

declare(strict_types=1);

namespace Connetation\Elements\DataProcessing;

use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

final class AlertSettingsProcessor implements DataProcessorInterface
{
    private const SETTINGS_FIELD = 'tx_conn_elements_alert_settings';
    private const STYLES = ['info', 'success', 'warning', 'danger', 'custom'];
    private const POSITIONS = ['static', 'fixed-top', 'fixed-bottom', 'edge-left', 'edge-right'];
    private const VERTICAL_ALIGNS = ['top', 'center', 'bottom'];
    private const BUTTON_SHAPES = ['circle', 'square'];
    // Left/right edge positions are always a small button that expands on
    // click (there's no full-width bar variant for them); top/bottom are
    // always the full-width bar (no button/collapse variant). Position
    // alone decides this now — settings.compact was dropped.
    private const EDGE_POSITIONS = ['edge-left', 'edge-right'];

    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData
    ): array {
        $flexFormService = GeneralUtility::makeInstance(FlexFormService::class);
        $flexData = $flexFormService->convertFlexFormContentToArray(
            (string)($processedData['data'][self::SETTINGS_FIELD] ?? '')
        );
        $raw = $flexData['settings'] ?? [];

        $style = $raw['style'] ?? 'info';
        $position = $raw['position'] ?? 'static';
        $position = in_array($position, self::POSITIONS, true) ? $position : 'static';
        $verticalAlign = $raw['verticalAlign'] ?? 'center';
        $verticalAlign = in_array($verticalAlign, self::VERTICAL_ALIGNS, true) ? $verticalAlign : 'center';
        $buttonShape = $raw['buttonShape'] ?? 'circle';
        $buttonShape = in_array($buttonShape, self::BUTTON_SHAPES, true) ? $buttonShape : 'circle';
        $isEdgeButton = in_array($position, self::EDGE_POSITIONS, true);

        $settings = [
            'style'               => in_array($style, self::STYLES, true) ? $style : 'info',
            'customAccentColor'   => $this->resolveHexColor($raw['customAccentColor'] ?? ''),
            'customBackgroundColor' => $this->resolveHexColor($raw['customBackgroundColor'] ?? ''),
            'customTextColor'     => $this->resolveHexColor($raw['customTextColor'] ?? ''),
            'icon'                => !empty($raw['icon'] ?? true),
            'dismissible'         => !empty($raw['dismissible']),
            'closeColor'          => $this->resolveHexColor($raw['closeColor'] ?? ''),
            'position'            => $position,
            'isFixed'             => $position !== 'static',
            // Only Top/Bottom (a full-width bar) push page content — the
            // edge buttons float over the page like any other floating
            // action button, they don't reserve space for themselves.
            'pushContent'         => $position === 'static' || $isEdgeButton ? false : !empty($raw['pushContent'] ?? true),
            // Edge buttons alone are the "compact" icon-that-expands
            // widget; Top/Bottom are always the full bar. Position alone
            // decides this now (settings.compact was dropped).
            'compact'             => $isEdgeButton,
            'verticalAlign'       => $verticalAlign,
            'buttonShape'         => $buttonShape,
        ];

        $typoScriptSettings = GeneralUtility::makeInstance(TypoScriptService::class)
            ->convertTypoScriptArrayToPlainArray($contentObjectConfiguration['settings.'] ?? []);

        $processedData['settings'] = array_replace_recursive($typoScriptSettings, $settings);

        return $processedData; 
    }

    private function resolveHexColor(string $value): ?string
    {
        $value = trim($value);

        return preg_match('/^#[0-9a-fA-F]{6}([0-9a-fA-F]{2})?$/', $value) === 1
            ? $value
            : null;
    }

}
