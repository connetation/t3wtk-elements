<?php

declare(strict_types=1);

namespace Connetation\T3wtkElements\DataProcessing;

use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\ContentObject\DataProcessorInterface;

final class QuoteSettingsProcessor implements DataProcessorInterface
{
    private const SETTINGS_FIELD = 'tx_conn_t3wtk_elements_quote_settings';
    private const STYLES = ['mark', 'minimal', 'simple', 'container', 'sketch'];
    private const ALIGNMENTS = ['left', 'center', 'right'];
    private const TEXT_SIZES = ['small' => '1.1rem', 'large' => '1.6rem', 'xlarge' => '2rem'];

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

        $style = $raw['style'] ?? 'container';
        $align = $raw['align'] ?? 'center';

        $textSize = trim((string)($raw['textSize'] ?? ''));

        $settings = [
            'style'       => in_array($style, self::STYLES, true) ? $style : 'container',
            'align'       => in_array($align, self::ALIGNMENTS, true) ? $align : 'center',
            'accentColor' => trim((string)($raw['accentColor'] ?? '')) ?: null,
            'textColor'   => trim((string)($raw['textColor'] ?? '')) ?: null,
            'textSize'    => self::TEXT_SIZES[$textSize] ?? null,
        ];

        $typoScriptSettings = GeneralUtility::makeInstance(TypoScriptService::class)
            ->convertTypoScriptArrayToPlainArray($contentObjectConfiguration['settings.'] ?? []);

        $processedData['settings'] = array_replace_recursive($typoScriptSettings, $settings);

        return $processedData;
    }
}
