<?php

declare(strict_types=1);

/*
 * This file is part of the Connetation TYPO3 Web Toolkit - T3WTK.
 *
 * Copyright (c) Connetation Web Engineering GmbH
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Connetation\T3wtkElements\DataProcessing;

use Connetation\T3wtkBase\Utility\CardAlignOptions;
use Connetation\T3wtkBase\Utility\RadiusOptions;
use Connetation\T3wtkBase\DataProcessing\AbstractItemsProcessor;
use Connetation\T3wtkElements\Service\ButtonStyleItemsProcFunc;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\ParameterType;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use Connetation\T3wtkBase\Utility\ResponsiveValue;

/**
 * People content element 
 */
final class PeopleProcessor extends AbstractItemsProcessor
{
    private const TABLE = 'tx_conn_t3wtk_elements_domain_model_person';

    private const STYLES = [
        'reveal-sheet',
        'reveal-curtain',
        'reveal-spotlight',
        'flippable-card',
        'team-card',
        'contact-card',
        'visit-card',
        'contact-banner',
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
        return $processorConfiguration['settingsField'] ?? 'tx_conn_t3wtk_elements_people_settings';
    }

    protected function resolveSettings(array $raw, string $settingsField): array
    {
        // Per device ("desktop,tablet,mobile"); a former single value is desktop
        // only. Empty tablet/mobile = automatic: at most 2-up on a tablet and
        // always 1-per-row on a phone (a cramped multi-column grid there made
        // the cards unreadable — names wrapped onto 2-3 lines).
        $columns = ResponsiveValue::parse($raw['columns'] ?? null, 1, 6, 4);

        $style = in_array($raw['style'] ?? '', self::STYLES, true) ? $raw['style'] : 'reveal-sheet';

        // Visit Card is a fixed 16:9 business-card shape and Contact Banner a
        // wide photo + text row — past 2-up both get squeezed too narrow.
        if ($style === 'visit-card' || $style === 'contact-banner') {
            $columns = array_map(static fn (?int $value): ?int => $value === null ? null : min($value, 2), $columns);
        }

        $showShadow  = (bool)($raw['showShadow'] ?? true);
        $shadowValue = $showShadow ? 'var(--conn-shadow-card)' : 'none';

        return [
            'source'          => ($raw['source'] ?? 'manual') === 'folder' ? 'folder' : 'manual',
            'folderUid'       => $this->firstUidFromGroupField((string)($raw['folder'] ?? '')),
            'peopleUids'      => $this->uidsFromGroupField((string)($raw['people'] ?? '')),
            'style'           => $style,
            'columns'         => $columns['desktop'],
            'colClass'        => '',
            'rowColsClass'    => ResponsiveValue::rowColsClass($columns),
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
            'spotlightBg'      => $this->sanitizeColor((string)($raw['spotlightBg'] ?? ''), ''),
            'spotlightTextColor' => $this->sanitizeColor((string)($raw['spotlightTextColor'] ?? ''), ''),
            'visitBandColor'   => $this->sanitizeColor((string)($raw['visitBandColor'] ?? ''), ''),
            'bannerIntro'       => trim((string)($raw['bannerIntro'] ?? '')),
            'bannerButtonLabel' => trim((string)($raw['bannerButtonLabel'] ?? '')),
            'bannerButtonLink'  => trim((string)($raw['bannerButtonLink'] ?? '')),
            'bannerBg'          => $this->sanitizeColor((string)($raw['bannerBg'] ?? ''), ''),
            'bannerBorderColor' => $this->sanitizeColor((string)($raw['bannerBorderColor'] ?? ''), ''),
            'bannerBorderColorEnd' => $this->sanitizeColor((string)($raw['bannerBorderColorEnd'] ?? ''), ''),
            'bannerSpin'        => (bool)($raw['bannerSpin'] ?? true),
            'bannerTextColor'   => $this->sanitizeColor((string)($raw['bannerTextColor'] ?? ''), ''),
            'bannerButtonStyle' => ButtonStyleItemsProcFunc::isButtonClass(trim((string)($raw['bannerButtonStyle'] ?? '')))
                ? trim((string)$raw['bannerButtonStyle'])
                : ButtonStyleItemsProcFunc::DEFAULT,
            'bannerButtonOutline' => str_contains((string)($raw['bannerButtonStyle'] ?? ''), 'btn-outline-'),
            'bannerButtonBg'    => $this->sanitizeColor((string)($raw['bannerButtonBg'] ?? ''), ''),
            'bannerButtonTextColor' => $this->sanitizeColor((string)($raw['bannerButtonTextColor'] ?? ''), ''),
        ] + CardAlignOptions::resolveSettings($raw);
    }

    protected function defaultSettings(string $settingsField): array
    {
        return [
            'source'          => 'manual',
            'folderUid'       => 0,
            'peopleUids'      => [],
            'style'           => 'reveal-sheet',
            'columns'         => 4,
            'colClass'        => '',
            'rowColsClass'    => ResponsiveValue::rowColsClass(['desktop' => 4, 'tablet' => null, 'mobile' => null]),
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
            'spotlightBg'      => '',
            'spotlightTextColor' => '',
            'visitBandColor'   => '',
            'bannerIntro'       => '',
            'bannerButtonLabel' => '',
            'bannerButtonLink'  => '',
            'bannerBg'          => '',
            'bannerBorderColor' => '',
            'bannerBorderColorEnd' => '',
            'bannerSpin'        => true,
            'bannerTextColor'   => '',
            'bannerButtonStyle' => ButtonStyleItemsProcFunc::DEFAULT,
            'bannerButtonOutline' => false,
            'bannerButtonBg'    => '',
            'bannerButtonTextColor' => '',
        ] + CardAlignOptions::DEFAULTS;
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
