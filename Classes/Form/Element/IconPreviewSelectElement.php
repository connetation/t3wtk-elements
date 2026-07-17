<?php

declare(strict_types=1);

namespace Connetation\Elements\Form\Element;

use Connetation\Elements\Service\IconAssetUrlResolver;
use Connetation\Elements\Service\IconItemsProcFunc;
use TYPO3\CMS\Backend\Form\Element\SelectSingleElement;
use TYPO3\CMS\Backend\Form\InlineStackProcessor;
use TYPO3\CMS\Core\Page\JavaScriptModuleInstruction;

final class IconPreviewSelectElement extends SelectSingleElement
{
    private const IONICON_STYLE_SUFFIXES = [
        'filled'  => '',
        'outline' => '-outline',
        'sharp'   => '-sharp',
    ];

    public function __construct(
        InlineStackProcessor $inlineStackProcessor,
        private readonly IconAssetUrlResolver $iconAssetUrlResolver,
        private readonly IconItemsProcFunc $iconItemsProcFunc,
    ) {
        parent::__construct($inlineStackProcessor);
    }

    public function render(): array
    {
        $row = $this->data['databaseRow'] ?? [];
        $config = $this->data['parameterArray']['fieldConf']['config'] ?? [];

        // "library"/"style" are optional companion fields (TextIcon has them,
        // for switching between Bootstrap Icons/Font Awesome/Ionicons) — a
        // field with no such switcher (e.g. a plain icon picker always using
        // Bootstrap Icons) simply won't have these columns, and IconItemsProcFunc
        // already defaults to bootstrap-icons/solid when they're absent.
        // The "name" field, in contrast, is always THIS field — its real TCA
        // column, whatever it's called on whichever table — never a
        // hardcoded assumption, so this element is reusable outside TextIcon.
        $libraryFieldName = (string)($config['iconLibraryField'] ?? 'tx_conn_elements_text_icon_library');
        $styleFieldName   = (string)($config['iconStyleField'] ?? 'tx_conn_elements_text_icon_style');
        $nameFieldName    = (string)($this->data['fieldName'] ?? 'tx_conn_elements_text_icon_name');

        $library = $this->firstValue($row[$libraryFieldName] ?? 'bootstrap-icons');
        $style   = $this->iconItemsProcFunc->normalizeStyle($library, $this->firstValue($row[$styleFieldName] ?? 'solid'));
        $name    = $this->firstValue($row[$nameFieldName] ?? '');
        $request = $this->data['request'] ?? null;

        // Switching "Icon library" (or "Icon style") can leave a previously
        // selected icon that no longer exists for the new combination — fall
        // back to the first available icon instead of an orphaned value.
        // A never-touched (empty) selection is left alone.
        if ($name !== '' && !$this->iconItemsProcFunc->isValidName($library, $style, $name)) {
            $name = $this->iconItemsProcFunc->firstAvailableName($library, $style);
            $this->data['databaseRow'][$nameFieldName] = $name;
            $this->data['parameterArray']['itemFormElValue'] = $name;
        }

        $resultArray = parent::render();

        // The "Icon" field can sit in a different palette/fieldset than
        // "library"/"style" (it's rendered standalone for full width), so the
        // JS can't rely on DOM proximity to find its sibling fields. Derive
        // the record-specific field-name prefix here, where it's directly
        // available, and let JS target siblings by exact name (emitted below
        // as data-*-field attributes) instead.
        $itemFormElName = (string)($this->data['parameterArray']['itemFormElName'] ?? '');
        $recordPrefix   = str_ends_with($itemFormElName, '[' . $nameFieldName . ']')
            ? substr($itemFormElName, 0, -strlen('[' . $nameFieldName . ']'))
            : '';

        $iconMarkup = $this->buildIconMarkup($library, $style, $name, $request);
        $ioniconsBaseUrl = $this->iconAssetUrlResolver->resolve(
            'EXT:conn_elements/Resources/Public/Icons/Ionicons/accessibility.svg',
            $request
        );
        $ioniconsBaseUrl = substr($ioniconsBaseUrl, 0, -strlen('accessibility.svg'));

        $resultArray['stylesheetFiles'][] = 'EXT:conn_elements/Resources/Public/Css/bootstrap-icons.css';
        $resultArray['stylesheetFiles'][] = 'EXT:conn_elements/Resources/Public/Css/font-awesome.min.css';
        $resultArray['stylesheetFiles'][] = 'EXT:conn_elements/Resources/Public/Css/icon-preview.css';

        $resultArray['html'] .= '<div class="conn-icon-picker"'
            . ' data-ionicons-base="' . htmlspecialchars($ioniconsBaseUrl) . '"'
            . ' data-record-prefix="' . htmlspecialchars($recordPrefix) . '"'
            . ' data-library-field="' . htmlspecialchars($libraryFieldName) . '"'
            . ' data-style-field="' . htmlspecialchars($styleFieldName) . '"'
            . ' data-name-field="' . htmlspecialchars($nameFieldName) . '">'
            . '<div class="conn-icon-preview"><i class="' . htmlspecialchars($iconMarkup['class']) . '" aria-hidden="true" style="--conn-icon-mask:' . htmlspecialchars($iconMarkup['mask']) . ';"></i></div>'
            . '<button type="button" class="btn btn-default btn-sm conn-icon-picker__toggle">Browse icons&hellip;</button>'
            . '<div class="conn-icon-picker__panel" hidden>'
                . '<input type="text" class="form-control conn-icon-picker__search" placeholder="Search icons&hellip;" autocomplete="off">'
                . '<div class="conn-icon-picker__grid"></div>'
                . '<p class="conn-icon-picker__status"></p>'
            . '</div>'
            . '</div>';
        $resultArray['javaScriptModules'][] = JavaScriptModuleInstruction::create('@conn/elements/icon-preview.js');

        return $resultArray;
    }

    private function buildIconMarkup(string $library, string $style, string $name, mixed $request): array
    {
        if ($name === '') {
            return ['class' => '', 'mask' => ''];
        }

        if ($library === 'font-awesome') {
            return ['class' => 'fa-' . $style . ' fa-' . $name, 'mask' => ''];
        }

        if ($library === 'ionicons') {
            $suffix = self::IONICON_STYLE_SUFFIXES[$style] ?? '';
            $url    = $this->iconAssetUrlResolver->resolve(
                'EXT:conn_elements/Resources/Public/Icons/Ionicons/' . $name . $suffix . '.svg',
                $request
            );

            return ['class' => 'conn-icon--ionicon', 'mask' => 'url(' . $url . ')'];
        }

        return ['class' => 'bi bi-' . $name, 'mask' => ''];
    }

    private function firstValue(mixed $value): string
    {
        if (is_array($value)) {
            $value = $value[0] ?? '';
        }

        return (string)$value;
    }
}
