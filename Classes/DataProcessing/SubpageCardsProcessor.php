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

use Connetation\T3wtkBase\Utility\RadiusOptions;
use Connetation\T3wtkBase\DataProcessing\AbstractItemsProcessor;
use Connetation\T3wtkBase\Utility\CardAlignOptions;
use Connetation\T3wtkBase\Utility\FontWeightOptions;
use TYPO3\CMS\Core\Cache\CacheTag;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Connetation\T3wtkBase\Utility\ResponsiveValue;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Subpage Cards: one card per page record instead of per child record.
 *
 * Page source (tt_content.tx_conn_t3wtk_elements_subpage_cards_source):
 *   ''         → subpages of the current page (default)
 *   'subpages' → subpages of the pages selected in tt_content.pages
 *                (falls back to the current page when nothing is selected)
 *   'selected' → the pages selected in tt_content.pages themselves
 *
 * Card data comes from the page's "Card" tab; teaser and media fall back to
 * the page's abstract/media fields, the link falls back to the page itself.
 *
 * Pages are fetched through PageRepository::getMenu()/getMenuForPages(), so
 * enable fields, fe_group access, workspace and language overlays behave
 * exactly like core menus. process() is overridden because the rows are not
 * content_element children; the settings merge and batched FAL helpers of
 * AbstractItemsProcessor are reused.
 */
final class SubpageCardsProcessor extends AbstractItemsProcessor
{
    private const SOURCE_FIELD = 'tx_conn_t3wtk_elements_subpage_cards_source';

    /** Doktypes rendered as cards: standard, external link, shortcut. */
    private const DOKTYPES = [
        PageRepository::DOKTYPE_DEFAULT,
        PageRepository::DOKTYPE_LINK,
        PageRepository::DOKTYPE_SHORTCUT,
    ];

    private const ORDER_BY = [
        'sorting'     => 'sorting',
        'title'       => 'title',
        'crdate'      => 'crdate DESC',
        'lastUpdated' => 'lastUpdated DESC, tstamp DESC',
    ];

    public function process(
        ContentObjectRenderer $cObj,
        array $contentObjectConfiguration,
        array $processorConfiguration,
        array $processedData
    ): array {
        $targetVar     = $processorConfiguration['as'] ?? $this->getDefaultTargetVariable();
        $settingsField = $this->getSettingsField($processorConfiguration);
        $record        = $processedData['data'] ?? [];

        $settings = (int)($record['uid'] ?? 0) === 0
            ? $this->defaultSettings($settingsField)
            : $this->resolveSettings($this->decodeFlexFormSettings($record, $settingsField), $settingsField);

        $processedData['settings'] = array_replace_recursive(
            $this->getTypoScriptSettings($contentObjectConfiguration),
            $settings
        );

        if ((int)($record['uid'] ?? 0) === 0) {
            $processedData[$targetVar] = [];
            return $processedData;
        }

        $pageRepository = GeneralUtility::makeInstance(PageRepository::class);
        [$pages, $parentUids] = $this->fetchPages($pageRepository, $cObj, $record, $settings);

        $cards = $this->buildCards($pages);
        $this->addCacheTags($cObj, array_merge($parentUids, array_column($cards, 'uid')));

        $processedData[$targetVar] = $cards;

        return $processedData;
    }

    protected function getTable(): string
    {
        return 'pages';
    }

    protected function getDefaultTargetVariable(): string
    {
        return 'cards';
    }

    protected function getSettingsField(array $processorConfiguration): string
    {
        return $processorConfiguration['settingsField'] ?? 'tx_conn_t3wtk_elements_subpage_cards_settings';
    }

    protected function resolveSettings(array $raw, string $settingsField): array
    {
        $defaults = $this->defaultSettings($settingsField);

        // Per device ("desktop,tablet,mobile"); a former single value is desktop only.
        $columns = ResponsiveValue::parse($raw['columns'] ?? null, 1, 4, $defaults['columns']);

        $borderColor  = $this->sanitizeHexColor((string)($raw['cardBorderColor'] ?? ''), '');
        // Former free-text lengths (e.g. "8px") are not an option any more and
        // fall back to the theme radius.
        $borderRadius = RadiusOptions::resolve($raw['cardBorderRadius'] ?? null);

        // CSS custom properties consumed by conn_t3wtk_base's card tokens and
        // the Card Group styles — empty values keep the theme defaults.
        $styleVars = '';
        if ($borderColor !== '') {
            $styleVars .= '--conn-card-border-color:' . $borderColor . ';';
        }

        $titleAlign = $this->allow($raw['titleAlign'] ?? null, ['start', 'center', 'end'], 'start');

        return [
            'columns'           => $columns['desktop'],
            'colClass'          => '',
            'rowColsClass'      => ResponsiveValue::rowColsClass($columns),
            'style'             => $this->allow($raw['style'] ?? null, ['default', 'shadow', 'flat'], $defaults['style']),
            'accentBar'         => (bool)($raw['accentBar'] ?? $defaults['accentBar']),
            'cardBorderColor'   => $borderColor,
            'cardBorderRadius'  => $borderRadius,
            'styleVars'         => $styleVars,
            'titleTag'          => $this->allow($raw['titleTag'] ?? null, ['h2', 'h3', 'h4', 'h5', 'p'], $defaults['titleTag']),
            'titleAlign'        => $titleAlign,
            'bodyAlign'         => $this->allow($raw['bodyAlign'] ?? null, ['start', 'center', 'end'], 'start'),
            'linkAlign'         => $this->allow($raw['linkAlign'] ?? null, ['start', 'center', 'end'], 'start'),
            'showImage'         => (bool)($raw['showImage'] ?? $defaults['showImage']),
            'showPlaceholder'   => (bool)($raw['showPlaceholder'] ?? $defaults['showPlaceholder']),
            'showSubtitle'      => (bool)($raw['showSubtitle'] ?? $defaults['showSubtitle']),
            'subtitleEyebrow'   => (bool)($raw['subtitleEyebrow'] ?? $defaults['subtitleEyebrow']),
            'eyebrowAlignClass' => $this->resolveAlignClass($raw['eyebrowPosition'] ?? null, $titleAlign),
            'showTeaser'        => (bool)($raw['showTeaser'] ?? $defaults['showTeaser']),
            'cropMaxCharacters' => max(0, (int)($raw['cropMaxCharacters'] ?? 0)),
            'showLink'          => (bool)($raw['showLink'] ?? $defaults['showLink']),
            'linkLabel'         => trim((string)($raw['linkLabel'] ?? '')),
            'orderBy'           => $this->allow($raw['orderBy'] ?? null, array_keys(self::ORDER_BY), 'sorting'),
            'maxItems'          => max(0, (int)($raw['maxItems'] ?? 0)),
            'includeNavHide'    => (bool)($raw['includeNavHide'] ?? false),
        ] + FontWeightOptions::resolveCard($raw) + CardAlignOptions::resolveSettings($raw);
    }

    protected function defaultSettings(string $settingsField): array
    {
        return [
            'columns'           => 3,
            'colClass'          => '',
            'rowColsClass'      => ResponsiveValue::rowColsClass(['desktop' => 3, 'tablet' => null, 'mobile' => null]),
            'style'             => 'default',
            'accentBar'         => true,
            'cardBorderColor'   => '',
            'cardBorderRadius'  => RadiusOptions::THEME_DEFAULT,
            'styleVars'         => '',
            'titleTag'          => 'h3',
            'titleAlign'        => 'start',
            'bodyAlign'         => 'start',
            'linkAlign'         => 'start',
            'showImage'         => true,
            'showPlaceholder'   => false,
            'showSubtitle'      => true,
            'subtitleEyebrow'   => false,
            'eyebrowAlignClass' => 'text-start',
            'showTeaser'        => true,
            'cropMaxCharacters' => 0,
            'showLink'          => true,
            'linkLabel'         => '',
            'orderBy'           => 'sorting',
            'maxItems'          => 0,
            'includeNavHide'    => false,
        ] + FontWeightOptions::CARD_DEFAULTS + CardAlignOptions::DEFAULTS;
    }

    // ─────────────────────────────────────────────────────────────
    // Page selection
    // ─────────────────────────────────────────────────────────────

    /**
     * @return array{0: array<int, array>, 1: int[]} page rows (in render order)
     *         and the parent page uids whose subpages were listed
     */
    private function fetchPages(
        PageRepository $pageRepository,
        ContentObjectRenderer $cObj,
        array $record,
        array $settings
    ): array {
        $source      = $this->allow($record[self::SOURCE_FIELD] ?? null, ['', 'subpages', 'selected'], '');
        $selectedIds = $this->parsePageUids((string)($record['pages'] ?? ''));

        // Integers only — safe to inline into the additional WHERE clause
        $where = ' AND doktype IN (' . implode(',', self::DOKTYPES) . ')';
        if (!$settings['includeNavHide']) {
            $where .= ' AND nav_hide = 0';
        }
        $orderBy = self::ORDER_BY[$settings['orderBy']];

        if ($source === 'selected') {
            if ($selectedIds === []) {
                return [[], []];
            }
            $pages = $pageRepository->getMenuForPages($selectedIds, '*', $orderBy, $where);
            if ($settings['orderBy'] === 'sorting') {
                // Keep the editor's selection order instead of the page tree order
                $pages = array_filter(array_replace(array_fill_keys($selectedIds, null), $pages));
            }
            return [$this->finalizePages($pageRepository, $pages, $settings), []];
        }

        $parentUids = $source === 'subpages' && $selectedIds !== []
            ? $selectedIds
            : [$this->getCurrentPageId($cObj, $record)];

        if ($settings['orderBy'] === 'sorting') {
            // Group by parent in selection order, page tree order within each
            $pages = [];
            foreach ($parentUids as $parentUid) {
                $pages += $pageRepository->getMenu($parentUid, '*', $orderBy, $where);
            }
        } else {
            $pages = $pageRepository->getMenu($parentUids, '*', $orderBy, $where);
        }

        return [$this->finalizePages($pageRepository, $pages, $settings), $parentUids];
    }

    /** Drop pages not available in the current language, then apply the limit. */
    private function finalizePages(PageRepository $pageRepository, array $pages, array $settings): array
    {
        $languageAspect = GeneralUtility::makeInstance(Context::class)->getAspect('language');
        $pages = array_values(array_filter(
            $pages,
            static fn(array $page): bool => $pageRepository->isPageSuitableForLanguage($page, $languageAspect)
        ));

        return $settings['maxItems'] > 0 ? array_slice($pages, 0, $settings['maxItems']) : $pages;
    }

    private function getCurrentPageId(ContentObjectRenderer $cObj, array $record): int
    {
        $pageInformation = $cObj->getRequest()->getAttribute('frontend.page.information');

        return $pageInformation !== null ? $pageInformation->getId() : (int)($record['pid'] ?? 0);
    }

    /** tt_content.pages is a comma list of uids (optionally "pages_<uid>"). */
    private function parsePageUids(string $value): array
    {
        $uids = [];
        foreach (explode(',', $value) as $part) {
            $uid = (int)preg_replace('/^pages_/', '', trim($part));
            if ($uid > 0) {
                $uids[] = $uid;
            }
        }

        return array_values(array_unique($uids));
    }

    // ─────────────────────────────────────────────────────────────
    // Card rows
    // ─────────────────────────────────────────────────────────────

    private function buildCards(array $pages): array
    {
        if ($pages === []) {
            return [];
        }

        // File references of a translated page belong to the overlay record
        $referenceUids = [];
        foreach ($pages as $page) {
            $referenceUids[] = (int)($page['_LOCALIZED_UID'] ?? $page['uid']);
        }
        $cardMedia = $this->resolveFileReferences('pages', 'tx_conn_t3wtk_elements_card_media', $referenceUids);
        $pageMedia = $this->resolveFileReferences('pages', 'media', $referenceUids);

        $cards = [];
        foreach ($pages as $page) {
            $uid          = (int)$page['uid'];
            $referenceUid = (int)($page['_LOCALIZED_UID'] ?? $uid);
            $subtitle     = trim((string)($page['tx_conn_t3wtk_elements_card_subtitle'] ?? ''));
            $teaser       = trim((string)($page['tx_conn_t3wtk_elements_card_teaser'] ?? ''));
            $link         = trim((string)($page['tx_conn_t3wtk_elements_card_link'] ?? ''));

            $cards[] = [
                'uid'       => $uid,
                'title'     => (string)$page['title'],
                'subtitle'  => $subtitle !== '' ? $subtitle : trim((string)($page['subtitle'] ?? '')),
                'teaser'    => $teaser !== '' ? $teaser : trim((string)($page['abstract'] ?? '')),
                'link'      => $link !== '' ? $link : 't3://page?uid=' . $uid,
                'imageFile' => $cardMedia[$referenceUid][0] ?? $pageMedia[$referenceUid][0] ?? null,
                'page'      => $page,
            ];
        }

        return $cards;
    }

    /**
     * Card content lives on other page records: tag this page's cache with
     * them (and the listed parents, so new/moved subpages show up) — the
     * DataHandler flushes "pageId_<uid>" whenever such a page is edited.
     */
    private function addCacheTags(ContentObjectRenderer $cObj, array $pageUids): void
    {
        $cacheCollector = $cObj->getRequest()->getAttribute('frontend.cache.collector');
        if ($cacheCollector === null || $pageUids === []) {
            return;
        }

        $cacheCollector->addCacheTags(...array_map(
            static fn(int $uid): CacheTag => new CacheTag('pageId_' . $uid),
            array_values(array_unique(array_map('intval', $pageUids)))
        ));
    }

    // ─────────────────────────────────────────────────────────────
    // Value guards
    // ─────────────────────────────────────────────────────────────

    private function allow(mixed $value, array $allowed, string|int $default): string|int
    {
        return in_array($value, $allowed, true) ? $value : $default;
    }
}
