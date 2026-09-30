<?php

declare(strict_types=1);

namespace Connetation\T3kitElements\DataProcessing;

use Connetation\T3kitBase\Utility\RadiusOptions;
use Connetation\T3kitBase\DataProcessing\AbstractItemsProcessor;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\ParameterType;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * People content element 
 */
final class PeopleProcessor extends AbstractItemsProcessor
{
    private const TABLE = 'tx_conn_t3kit_elements_domain_model_person';

    private const STYLES = [
        'reveal-sheet',
        'reveal-curtain',
        'reveal-spotlight',
        'flippable-card',
        'team-card',
        'contact-card',
        'visit-card',
    ];

    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData
    ): array {
        $targetVar     = $processorConfiguration['as'] ?? $this->getDefaultTargetVariable();
        $settingsField = $this->getSettingsField($processorConfiguration);

        $raw      = $this->decodeFlexFormSettings($processedData['data'] ?? [], $settingsField);
        $settings = $this->resolveSettings($raw, $settingsField);

        $rows = $settings['source'] === 'folder'
            ? $this->fetchByFolder($settings['folderUid'])
            : $this->fetchByUidsOrdered($settings['peopleUids']);

        $rows = $this->attachFirstFileReference($rows, self::TABLE, 'image', 'imageFile');
        $rows = $this->enrichRows($rows, $settings);

        $processedData[$targetVar] = $rows;
        $processedData['settings'] = array_replace_recursive(
            $this->getTypoScriptSettings($contentObjectConfiguration),
            $settings
        );

        return $processedData;
    }

    protected function getTable(): string
    {
        return self::TABLE;
    }

    protected function getDefaultTargetVariable(): string
    {
        return 'people';
    }

    protected function getSettingsField(array $processorConfiguration): string
    {
        return $processorConfiguration['settingsField'] ?? 'tx_conn_t3kit_elements_people_settings';
    }

    protected function resolveSettings(array $raw, string $settingsField): array
    {
        $columns = (int)($raw['columns'] ?? 4);
        // Mobile-first: always 1-per-row below 576px (col-12, no breakpoint
        // infix), regardless of the configured column count — a cramped
        // multi-column grid on a phone was part of what made cards unreadable.
        // 3-/4-up configs step the density down gradually instead of jumping
        // straight to their full column count at 768px (col-md-*), which left
        // team/reveal cards too narrow — names wrapped onto 2-3 lines on a
        // tablet. col-sm-6 holds 2-up through 991px, col-lg-4 gives 3-up from
        // 992px, and (for the 4-column config) col-xl-3 only reaches the full
        // 4-up at 1200px and wider.
        $colMap  = [
            1 => 'col-12',
            2 => 'col-12 col-sm-6',
            3 => 'col-12 col-sm-6 col-lg-4',
            4 => 'col-12 col-sm-6 col-lg-4 col-xl-3',
            6 => 'col-12 col-sm-6 col-md-2',
        ];

        $style = in_array($raw['style'] ?? '', self::STYLES, true) ? $raw['style'] : 'reveal-sheet';

        // Visit Card is a fixed 16:9 business-card shape — past 2-up it gets
        // squeezed so narrow the band/contact lines feel cramped.
        if ($style === 'visit-card' && $columns > 2) {
            $columns = 2;
        }

        $showShadow  = (bool)($raw['showShadow'] ?? true);
        $shadowValue = $showShadow ? 'var(--conn-shadow-card)' : 'none';

        return [
            'source'          => ($raw['source'] ?? 'manual') === 'folder' ? 'folder' : 'manual',
            'folderUid'       => $this->firstUidFromGroupField((string)($raw['folder'] ?? '')),
            'peopleUids'      => $this->uidsFromGroupField((string)($raw['people'] ?? '')),
            'style'           => $style,
            'columns'         => $columns,
            'colClass'        => $colMap[$columns] ?? $colMap[4],
            'align'           => in_array($raw['align'] ?? '', ['left', 'center', 'right'], true) ? $raw['align'] : 'center',
            'cardWidth'       => in_array($raw['cardWidth'] ?? '', ['fixed', 'fill'], true) ? $raw['cardWidth'] : 'fixed',
            'imageSideMode'   => in_array($raw['imageSideMode'] ?? '', ['left', 'right'], true) ? $raw['imageSideMode'] : 'auto',
            'textPosition'    => in_array($raw['textPosition'] ?? '', ['top', 'center', 'bottom'], true) ? $raw['textPosition'] : 'bottom',
            'textAlign'       => in_array($raw['textAlign'] ?? '', ['left', 'center', 'right'], true) ? $raw['textAlign'] : 'center',
            'showRole'        => (bool)($raw['showRole'] ?? true),
            'showBio'         => (bool)($raw['showBio'] ?? true),
            // Legacy records only have the former on/off "roundedCorners" toggle.
            'borderRadius'    => RadiusOptions::resolve(
                $raw['borderRadius'] ?? null,
                ($raw['roundedCorners'] ?? true) ? RadiusOptions::THEME_DEFAULT : 'none'
            ),
            'showBorder'      => (bool)($raw['showBorder'] ?? true),
            'showShadow'      => (bool)($raw['showShadow'] ?? true),
            'shadowValue'     => $shadowValue,
            'nametagBg'        => $this->sanitizeColor((string)($raw['nametagBg'] ?? ''), '#000000'),
            'nametagTextColor' => $this->sanitizeColor((string)($raw['nametagTextColor'] ?? ''), '#ffffff'),
            'revealBg'         => $this->sanitizeColor((string)($raw['revealBg'] ?? ''), ''),
            'revealTextColor'  => $this->sanitizeColor((string)($raw['revealTextColor'] ?? ''), ''),
            'curtainBg'        => $this->sanitizeColor((string)($raw['curtainBg'] ?? ''), ''),
            'curtainTextColor' => $this->sanitizeColor((string)($raw['curtainTextColor'] ?? ''), ''),
            'visitBandColor'   => $this->sanitizeColor((string)($raw['visitBandColor'] ?? ''), ''),
        ];
    }

    protected function defaultSettings(string $settingsField): array
    {
        return [
            'source'          => 'manual',
            'folderUid'       => 0,
            'peopleUids'      => [],
            'style'           => 'reveal-sheet',
            'columns'         => 4,
            'colClass'        => 'col-12 col-sm-6 col-lg-4 col-xl-3',
            'align'           => 'center',
            'cardWidth'       => 'fixed',
            'imageSideMode'   => 'auto',
            'textPosition'    => 'bottom',
            'textAlign'       => 'center',
            'showRole'        => true,
            'showBio'         => true,
            'borderRadius'    => RadiusOptions::THEME_DEFAULT,
            'showBorder'      => true,
            'showShadow'      => true,
            'shadowValue'     => 'var(--conn-shadow-card)',
            'nametagBg'        => '#000000',
            'nametagTextColor' => '#ffffff',
            'revealBg'         => '',
            'revealTextColor'  => '',
            'curtainBg'        => '',
            'curtainTextColor' => '',
            'visitBandColor'   => '',
        ];
    }

    // ─────────────────────────────────────────────────────────────
    // Fetching
    // ─────────────────────────────────────────────────────────────

    private function fetchByFolder(int $folderUid): array
    {
        if ($folderUid === 0) {
            return [];
        }

        $queryBuilder = $this->createFrontendQueryBuilder(self::TABLE);

        return $queryBuilder
            ->select('*')
            ->from(self::TABLE)
            ->where(
                $queryBuilder->expr()->eq(
                    'pid',
                    $queryBuilder->createNamedParameter($folderUid, ParameterType::INTEGER)
                )
            )
            ->orderBy('sorting', 'ASC')
            ->executeQuery()
            ->fetchAllAssociative();
    }

    /** Fetches by uid and re-orders the result to match the editor-chosen order (DB IN() doesn't preserve it). */
    private function fetchByUidsOrdered(array $uids): array
    {
        if ($uids === []) {
            return [];
        }

        $queryBuilder = $this->createFrontendQueryBuilder(self::TABLE);
        $rows = $queryBuilder
            ->select('*')
            ->from(self::TABLE)
            ->where(
                $queryBuilder->expr()->in(
                    'uid',
                    $queryBuilder->createNamedParameter($uids, ArrayParameterType::INTEGER)
                )
            )
            ->executeQuery()
            ->fetchAllAssociative();

        $byUid = [];
        foreach ($rows as $row) {
            $byUid[(int)$row['uid']] = $row;
        }

        $ordered = [];
        foreach ($uids as $uid) {
            if (isset($byUid[$uid])) {
                $ordered[] = $byUid[$uid];
            }
        }

        return $ordered;
    }

    // ─────────────────────────────────────────────────────────────
    // Row enrichment
    // ─────────────────────────────────────────────────────────────

    private function enrichRows(array $rows, array $settings): array
    {
        $imageSideMode = $settings['imageSideMode'] ?? 'auto';

        $i = 0;
        foreach ($rows as &$row) {
            $accent = $row['accent'] ?? 'auto';
            if (!in_array($accent, ['a', 'b'], true)) {
                $accent = $i % 2 === 0 ? 'a' : 'b';
            }

            // Element-level "All -> Left/Right" wins over the per-person record
            // field; the per-person field only matters while left on Auto.
            $imageSide = $imageSideMode !== 'auto' ? $imageSideMode : ($row['image_side'] ?? 'auto');
            if (!in_array($imageSide, ['left', 'right'], true)) {
                // Alternates in sync with accent: person 1 = left/A, person 2 = right/B, ...
                $imageSide = $i % 2 === 0 ? 'left' : 'right';
            }

            $row['accent']    = $accent;
            $row['imageSide'] = $imageSide;
            $row['initials']  = $this->initialsFromName((string)($row['name'] ?? ''));
            $i++;
        }
        unset($row);

        return $rows;
    }

    private function initialsFromName(string $name): string
    {
        $words = array_values(array_filter(preg_split('/\s+/', trim($name)) ?: []));
        $letters = array_map(
            static fn (string $word): string => mb_strtoupper(mb_substr($word, 0, 1)),
            array_slice($words, 0, 2)
        );

        return $letters === [] ? '?' : implode('', $letters);
    }

    // ─────────────────────────────────────────────────────────────
    // FlexForm "group" field parsing
    // ─────────────────────────────────────────────────────────────

    /** @return int[] */
    private function uidsFromGroupField(string $value): array
    {
        if (trim($value) === '') {
            return [];
        }

        $uids = [];
        foreach (explode(',', $value) as $item) {
            // "group" fields may store "uid" or "table_uid" depending on config; keep the numeric tail.
            $item = trim($item);
            if (preg_match('/(\d+)$/', $item, $matches) === 1) {
                $uids[] = (int)$matches[1];
            }
        }

        return array_values(array_filter($uids, static fn (int $uid): bool => $uid > 0));
    }

    private function firstUidFromGroupField(string $value): int
    {
        $uids = $this->uidsFromGroupField($value);

        return $uids[0] ?? 0;
    }

    private function sanitizeColor(string $value, string $default): string
    {
        $value = trim($value);

        return preg_match('/^#[0-9a-fA-F]{3,8}$/', $value) === 1 ? $value : $default;
    }
}
