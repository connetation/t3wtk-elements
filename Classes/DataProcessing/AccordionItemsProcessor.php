<?php

declare(strict_types=1);

namespace Connetation\Elements\DataProcessing;

use Connetation\Base\DataProcessing\AbstractItemsProcessor;

final class AccordionItemsProcessor extends AbstractItemsProcessor
{
    protected function getTable(): string
    {
        return 'tx_conn_elements_domain_model_accordion_item';
    }

    protected function getDefaultTargetVariable(): string
    {
        return 'items';
    }

    protected function getSettingsField(array $processorConfiguration): string
    {
        return 'tx_conn_elements_accordion_settings';
    }

    protected function postProcessRows(array $rows, array $settings): array
    {
        // Mark which item should be expanded on load
        foreach ($rows as $index => &$row) {
            $row['open'] = ($index === 0) && !$settings['collapseAll'];
        }
        unset($row);

        return $rows;
    }

    protected function resolveSettings(array $raw, string $settingsField): array
    {
        return [
            'style'          => ($raw['style'] ?? 'default') === 'flush' ? 'flush' : 'default',
            'alwaysOpen'     => !empty($raw['alwaysOpen']),
            'collapseAll'    => !empty($raw['collapseAll']),
            'useAsFaq'       => !empty($raw['useAsFaq']),
            'accordionClass' => 'accordion' . (($raw['style'] ?? '') === 'flush' ? ' accordion-flush' : ''),
        ];
    }

    protected function defaultSettings(string $settingsField): array
    {
        return [
            'style'          => 'default',
            'alwaysOpen'     => false,
            'collapseAll'    => false,
            'useAsFaq'       => false,
            'accordionClass' => 'accordion',
        ];
    }

    // Built here rather than in Fluid: json_encode() escapes quotes/control
    // characters correctly, which hand-assembling JSON in a template cannot
    // guarantee. The HEX_* flags additionally escape </>/&/'/" as \uXXXX so a
    // literal "</script>" inside an answer can't break out of the
    // <script type="application/ld+json"> tag it's rendered into.
    protected function finalizeProcessedData(
        array $processedData,
        array $rows,
        array $settings,
        string $settingsField,
        int $contentElementUid
    ): array {
        if (!$settings['useAsFaq']) {
            return $processedData;
        }

        $mainEntity = array_map(static function (array $row): array {
            return [
                '@type' => 'Question',
                'name'  => (string)($row['title'] ?? ''),
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text'  => trim(html_entity_decode(strip_tags((string)($row['body'] ?? '')), ENT_QUOTES)),
                ],
            ];
        }, $rows);

        $processedData['faqJsonLd'] = json_encode(
            [
                '@context'   => 'https://schema.org',
                '@type'      => 'FAQPage',
                'mainEntity' => $mainEntity,
            ],
            JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE
        );

        return $processedData;
    }
}
