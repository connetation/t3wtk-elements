<?php

declare(strict_types=1);

namespace Connetation\T3kitElements\Form\Element;

use Connetation\T3kitElements\Service\IconItemsProcFunc;
use TYPO3\CMS\Backend\Form\Element\SelectSingleElement;
use TYPO3\CMS\Backend\Form\InlineStackProcessor;

final class IconStyleSelectElement extends SelectSingleElement
{
    public function __construct(
        InlineStackProcessor $inlineStackProcessor,
        private readonly IconItemsProcFunc $iconItemsProcFunc,
    ) {
        parent::__construct($inlineStackProcessor);
    }

    public function render(): array
    {
        $row     = $this->data['databaseRow'] ?? [];
        $library = $this->firstValue($row['tx_conn_t3kit_elements_text_icon_library'] ?? 'bootstrap-icons');
        $style   = $this->firstValue($row['tx_conn_t3kit_elements_text_icon_style'] ?? 'solid');
        $normalized = $this->iconItemsProcFunc->normalizeStyle($library, $style);

        if ($normalized !== $style) {
            $this->data['databaseRow']['tx_conn_t3kit_elements_text_icon_style'] = $normalized;
            $this->data['parameterArray']['itemFormElValue'] = $normalized;
        }

        return parent::render();
    }

    private function firstValue(mixed $value): string
    {
        if (is_array($value)) {
            $value = $value[0] ?? '';
        }

        return (string)$value;
    }
}
