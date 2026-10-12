<?php

declare(strict_types=1);

/*
 * This file is part of the Connetation TYPO3 Web Toolkit - T3WTK.
 *
 * Copyright (c) Connetation Web Engineering GmbH
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
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

namespace Connetation\T3wtkElements\Upgrades;

use TYPO3\CMS\Core\Attribute\UpgradeWizard;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Upgrades\UpgradeWizardInterface;

/**
 * The extension key changed from "conn_elements" to "conn_t3wtk_elements", which moves
 * every "tx_conn_elements_*" table and tt_content column this package owns to
 * "tx_conn_t3wtk_elements_*". This wizard migrates the physical tables/columns in place so
 * existing content keeps working.
 *
 * Handles BOTH of these situations, because in practice a schema sync ("Analyze Database
 * Structure", `database:updateschema`, or another upgrade wizard's DatabaseUpdatedPrerequisite
 * firing first) can easily run before this wizard does — since ext_tables.sql already
 * declares the NEW names, that sync creates the new tables/columns empty, alongside the old
 * ones which still hold the real data:
 *
 *  1. Clean case — only the OLD table/column exists: rename it directly (RENAME TABLE /
 *     ALTER TABLE ... CHANGE), preserving the exact original column definition.
 *  2. Merge case — BOTH exist (old with real data, new already created empty by a schema
 *     sync): copy the data from old into new, then drop old. For tables this only happens
 *     when the new table is actually empty (a real row count mismatch is left untouched
 *     rather than silently discarded — re-run after resolving manually).
 *
 * This wizard deliberately declares NO prerequisites (in particular not
 * DatabaseUpdatedPrerequisite) so it never triggers that schema-add itself — but it cannot
 * stop something else from doing so first, hence handling both cases above.
 *
 * Only lists tables/columns this package itself declares. conn/dashboard and conn/wiki
 * happen to still use a few "tx_conn_elements_*"-prefixed tt_content columns of their own
 * (a leftover from when they were split out of this package) — those are untouched here.
 *
 * Also fixes sys_file_reference: a plain RENAME TABLE / ALTER TABLE CHANGE does NOT
 * retroactively update sys_file_reference.tablenames/fieldname, which point at the old
 * table/column by string, not by a real foreign key. Left alone, every image relation on
 * a renamed child table (card items, carousel slides, gallery items, …) or a renamed
 * tt_content image field (before/after images, video hero media, map illustration, …)
 * would silently stop resolving — the file exists, but TYPO3 looks it up under a
 * (tablenames, fieldname) pair that no longer matches anything.
 */
#[UpgradeWizard('connT3wtkElementsRenameExtensionKeySchema')]
readonly class RenameExtensionKeySchemaUpdate implements UpgradeWizardInterface
{
    private const OLD_PREFIX = 'tx_conn_elements_';
    private const NEW_PREFIX = 'tx_conn_t3wtk_elements_';

    private const TABLE_SUFFIXES = [
        'domain_model_mosaic_item',
        'domain_model_accordion_item',
        'domain_model_card_item',
        'domain_model_slide_item',
        'domain_model_stack_card',
        'domain_model_flip_carousel_item',
        'domain_model_carousel_slide',
        'domain_model_gallery_item',
        'domain_model_stat_item',
        'domain_model_logo_item',
        'domain_model_testimonial_item',
        'domain_model_pricing_tier',
        'domain_model_feature_item',
        'domain_model_timeline_item',
        'domain_model_person',
        'domain_model_tab_item',
        'domain_model_process_step',
    ];

    private const TT_CONTENT_COLUMN_SUFFIXES = [
        'accordion_items', 'accordion_settings',
        'card_group_items', 'card_group_settings',
        'hero_slider_items', 'hero_slider_settings',
        'slider_items', 'slider_settings',
        'thumbs_slider_items', 'thumbs_slider_settings',
        'parallax_slider_items', 'parallax_slider_settings', 'parallax_slider_bg_image',
        'text_animation',
        'card_stack_items', 'card_stack_settings',
        'flip_carousel_items', 'flip_carousel_settings',
        'flip_gallery_items', 'flip_gallery_settings',
        'mosaic_gallery_items', 'mosaic_gallery_settings',
        'carousel_items', 'carousel_settings',
        'timeline_items', 'timeline_settings',
        'stats_items', 'stats_settings',
        'logo_wall_items', 'logo_wall_settings',
        'testimonials_items', 'testimonials_settings',
        'pricing_tiers', 'pricing_settings',
        'feature_grid_items', 'feature_grid_settings',
        'people_settings',
        'text_icon_library', 'text_icon_style', 'text_icon_name',
        'text_icon_position', 'text_icon_shape', 'text_icon_size',
        'text_icon_color', 'text_icon_bg',
        'tabs_items', 'tabs_settings',
        'process_items', 'process_settings',
        'before_after_before', 'before_after_after',
        'before_after_before_label', 'before_after_after_label',
        'before_after_start', 'before_after_direction',
        'before_after_handle_icon', 'before_after_handle_color', 'before_after_handle_border_color',
        'quote_author', 'quote_role', 'quote_image', 'quote_settings',
        'alert_settings', 'alert_custom_icon_name', 'alert_custom_icon_image',
        'video_hero_media', 'video_hero_poster', 'video_hero_settings',
        'map_illustration_image', 'map_settings',
    ];

    /**
     * Subset of TT_CONTENT_COLUMN_SUFFIXES that are actual FAL image/media relations
     * (an int(11) reference count backing sys_file_reference rows) rather than an IRRE
     * item count, a flexform settings blob, or a plain varchar — these are the only ones
     * sys_file_reference.fieldname can be pointing at.
     */
    private const TT_CONTENT_FAL_FIELD_SUFFIXES = [
        'parallax_slider_bg_image',
        'before_after_before',
        'before_after_after',
        'before_after_handle_icon',
        'quote_image',
        'alert_custom_icon_image',
        'video_hero_media',
        'video_hero_poster',
        'map_illustration_image',
    ];

    public function __construct(
        private ConnectionPool $connectionPool,
    ) {}

    public function getTitle(): string
    {
        return 'EXT:conn_t3wtk_elements: Rename "tx_conn_elements_*" tables/columns to "tx_conn_t3wtk_elements_*"';
    }

    public function getDescription(): string
    {
        return sprintf(
            'The extension key changed from "conn_elements" to "conn_t3wtk_elements". This migrates '
            . 'the %d "tx_conn_elements_domain_model_*" tables and the %d "tx_conn_elements_*" columns '
            . 'on tt_content that this package owns to their "tx_conn_t3wtk_elements_*" equivalent, '
            . 'preserving all data — including merging in tables/columns a prior schema sync may '
            . 'already have created empty under the new name.',
            count(self::TABLE_SUFFIXES),
            count(self::TT_CONTENT_COLUMN_SUFFIXES)
        );
    }

    public function getPrerequisites(): array
    {
        return [];
    }

    public function updateNecessary(): bool
    {
        return $this->getTableActions() !== []
            || $this->getColumnActions() !== []
            || $this->getStaleFileReferenceTableNames() !== []
            || $this->getStaleFileReferenceFieldNames() !== [];
    }

    public function executeUpdate(): bool
    {
        // tt_content accumulates columns from every installed extension over the life of a
        // site; on many real installs its theoretical maximum row size (the sum of every
        // VARCHAR/CHAR column's max possible byte length) is already over InnoDB's
        // 8126-byte-not-counting-BLOBs limit, even though actual stored data fits fine.
        // MariaDB/MySQL re-validates that theoretical maximum on ANY structural ALTER
        // touching a VARCHAR/CHAR column — rename, drop, whatever — and rejects it with
        // "Row size too large", regardless of how little the operation actually changes.
        // innodb_strict_mode=0 downgrades that check to a warning, which is the standard,
        // TYPO3-core-documented way to get such an ALTER through; restore the session's
        // original value afterward rather than assuming it was 1.
        $connection = $this->connectionPool->getConnectionForTable('tt_content');
        $originalStrictMode = (string)$connection->executeQuery('SELECT @@SESSION.innodb_strict_mode')->fetchOne();
        $connection->executeStatement('SET SESSION innodb_strict_mode = 0');

        try {
            $this->migrateTables();
            $this->migrateTtContentColumns();
            $this->migrateFileReferences();
        } finally {
            $connection->executeStatement('SET SESSION innodb_strict_mode = ' . ($originalStrictMode !== '' ? (int)$originalStrictMode : 1));
        }

        return true;
    }

    /**
     * @return list<array{old: string, new: string, mode: 'rename'|'merge'|'conflict'}>
     */
    private function getTableActions(): array
    {
        $connection = $this->connectionPool->getConnectionForTable('tt_content');
        $schemaManager = $connection->createSchemaManager();
        $existingTables = array_flip($schemaManager->listTableNames());

        $actions = [];
        foreach (self::TABLE_SUFFIXES as $suffix) {
            $old = self::OLD_PREFIX . $suffix;
            $new = self::NEW_PREFIX . $suffix;

            if (!isset($existingTables[$old])) {
                continue;
            }

            if (!isset($existingTables[$new])) {
                $actions[] = ['old' => $old, 'new' => $new, 'mode' => 'rename'];
                continue;
            }

            $newRowCount = (int)$connection->executeQuery(sprintf(
                'SELECT COUNT(*) FROM %s',
                $connection->getDatabasePlatform()->quoteIdentifier($new)
            ))->fetchOne();

            $actions[] = ['old' => $old, 'new' => $new, 'mode' => $newRowCount === 0 ? 'merge' : 'conflict'];
        }

        return $actions;
    }

    /**
     * @return list<array{old: string, new: string, mode: 'rename'|'merge'}>
     */
    private function getColumnActions(): array
    {
        $connection = $this->connectionPool->getConnectionForTable('tt_content');
        $table = $connection->createSchemaManager()->introspectTable('tt_content');

        $actions = [];
        foreach (self::TT_CONTENT_COLUMN_SUFFIXES as $suffix) {
            $old = self::OLD_PREFIX . $suffix;
            $new = self::NEW_PREFIX . $suffix;

            if (!$table->hasColumn($old)) {
                continue;
            }

            $actions[] = ['old' => $old, 'new' => $new, 'mode' => $table->hasColumn($new) ? 'merge' : 'rename'];
        }

        return $actions;
    }

    private function migrateTables(): void
    {
        $connection = $this->connectionPool->getConnectionForTable('tt_content');
        $platform = $connection->getDatabasePlatform();

        foreach ($this->getTableActions() as $action) {
            if ($action['mode'] === 'conflict') {
                // Both old and new exist and new already has rows of its own — leave both
                // alone rather than guess which data wins; needs a manual look.
                continue;
            }

            if ($action['mode'] === 'merge') {
                // New table was already created (empty) by a prior schema sync — drop it
                // and rename the real one into its place.
                $connection->executeStatement(sprintf(
                    'DROP TABLE %s',
                    $platform->quoteIdentifier($action['new'])
                ));
            }

            $connection->executeStatement(sprintf(
                'RENAME TABLE %s TO %s',
                $platform->quoteIdentifier($action['old']),
                $platform->quoteIdentifier($action['new'])
            ));
        }
    }

    private function migrateTtContentColumns(): void
    {
        $connection = $this->connectionPool->getConnectionForTable('tt_content');
        $platform = $connection->getDatabasePlatform();

        foreach ($this->getColumnActions() as $action) {
            if ($action['mode'] === 'merge') {
                // New column was already created (empty/default) by a prior schema sync —
                // copy the real values over, then drop the old column.
                $connection->executeStatement(sprintf(
                    'UPDATE %s SET %s = %s',
                    $platform->quoteIdentifier('tt_content'),
                    $platform->quoteIdentifier($action['new']),
                    $platform->quoteIdentifier($action['old'])
                ));
                $connection->executeStatement(sprintf(
                    'ALTER TABLE %s DROP COLUMN %s',
                    $platform->quoteIdentifier('tt_content'),
                    $platform->quoteIdentifier($action['old'])
                ));
                continue;
            }

            // Clean rename: RENAME COLUMN is metadata-only (no table rebuild), unlike
            // CHANGE COLUMN which forces InnoDB to rebuild the whole row layout even when
            // only the name changes — on a table with as many columns as tt_content that
            // rebuild can exceed InnoDB's 8126-byte row-size limit ("Row size too large")
            // depending on the table's ROW_FORMAT, even though nothing about the actual
            // column definition is changing.
            $connection->executeStatement(sprintf(
                'ALTER TABLE %s RENAME COLUMN %s TO %s',
                $platform->quoteIdentifier('tt_content'),
                $platform->quoteIdentifier($action['old']),
                $platform->quoteIdentifier($action['new'])
            ));
        }
    }

    /**
     * @return list<array{old: string, new: string}>
     */
    private function getStaleFileReferenceTableNames(): array
    {
        $connection = $this->connectionPool->getConnectionForTable('sys_file_reference');

        $pairs = [];
        foreach (self::TABLE_SUFFIXES as $suffix) {
            $old = self::OLD_PREFIX . $suffix;
            $new = self::NEW_PREFIX . $suffix;

            $count = (int)$connection->count('uid', 'sys_file_reference', ['tablenames' => $old]);
            if ($count > 0) {
                $pairs[] = ['old' => $old, 'new' => $new];
            }
        }

        return $pairs;
    }

    /**
     * @return list<array{old: string, new: string}>
     */
    private function getStaleFileReferenceFieldNames(): array
    {
        $connection = $this->connectionPool->getConnectionForTable('sys_file_reference');

        $pairs = [];
        foreach (self::TT_CONTENT_FAL_FIELD_SUFFIXES as $suffix) {
            $old = self::OLD_PREFIX . $suffix;
            $new = self::NEW_PREFIX . $suffix;

            $count = (int)$connection->count('uid', 'sys_file_reference', [
                'tablenames' => 'tt_content',
                'fieldname' => $old,
            ]);
            if ($count > 0) {
                $pairs[] = ['old' => $old, 'new' => $new];
            }
        }

        return $pairs;
    }

    private function migrateFileReferences(): void
    {
        $connection = $this->connectionPool->getConnectionForTable('sys_file_reference');

        foreach ($this->getStaleFileReferenceTableNames() as $pair) {
            $connection->update(
                'sys_file_reference',
                ['tablenames' => $pair['new']],
                ['tablenames' => $pair['old']]
            );
        }

        foreach ($this->getStaleFileReferenceFieldNames() as $pair) {
            $connection->update(
                'sys_file_reference',
                ['fieldname' => $pair['new']],
                ['tablenames' => 'tt_content', 'fieldname' => $pair['old']]
            );
        }
    }
}
