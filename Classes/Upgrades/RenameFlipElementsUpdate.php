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
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Upgrades\UpgradeWizardInterface;

/**
 * "Flip Gallery" and "Flip Carousel" were named after the GSAP Flip plugin they animate
 * with, not after what editors see — neither element flips anything, which became
 * confusing once a real 3D "Flip Card" element was added. They were renamed:
 *
 *   conn_flip_gallery  => conn_lightbox_gallery  (grid that expands into a lightbox)
 *   conn_flip_carousel => conn_shift_carousel    (row that shifts one card per step)
 *
 * This wizard migrates existing installs: tt_content CType values, the tt_content
 * items/settings columns, the carousel's child table, sys_file_reference.tablenames of
 * the carousel's images (a string pointer, not a real foreign key, so a RENAME TABLE
 * alone would orphan them) and backend group permissions that name any of these.
 * The gallery's child table (…_domain_model_gallery_item) never carried "flip" in its
 * name and is unchanged.
 *
 * Accepts the current "tx_conn_t3wtk_elements_*", the pre-t3wtk "tx_conn_t3kit_elements_*"
 * and the pre-extension-key-rename "tx_conn_elements_*" source names, so it gives the same
 * result whether it runs before or after RenameExtensionKeySchemaUpdate /
 * MigrateFromT3kitUpdate (EXT:conn_t3wtk_base).
 *
 * Same "clean rename vs. merge" handling as RenameExtensionKeySchemaUpdate, and for the
 * same reason: ext_tables.sql already declares the NEW names, so a schema sync that runs
 * first creates them empty alongside the old ones that still hold the data. Declares NO
 * prerequisites so it never triggers that sync itself.
 */
#[UpgradeWizard('connT3wtkElementsRenameFlipElements')]
readonly class RenameFlipElementsUpdate implements UpgradeWizardInterface
{
    private const CTYPE_MAPPING = [
        'conn_flip_gallery'  => 'conn_lightbox_gallery',
        'conn_flip_carousel' => 'conn_shift_carousel',
    ];

    private const OLD_PREFIXES = ['tx_conn_t3wtk_elements_', 'tx_conn_t3kit_elements_', 'tx_conn_elements_'];
    private const NEW_PREFIX   = 'tx_conn_t3wtk_elements_';

    /** Child tables, old suffix => new suffix. */
    private const TABLE_SUFFIX_MAPPING = [
        'domain_model_flip_carousel_item' => 'domain_model_shift_carousel_item',
    ];

    /** tt_content columns, old suffix => new suffix. */
    private const TT_CONTENT_COLUMN_SUFFIX_MAPPING = [
        'flip_gallery_items'     => 'lightbox_gallery_items',
        'flip_gallery_settings'  => 'lightbox_gallery_settings',
        'flip_carousel_items'    => 'shift_carousel_items',
        'flip_carousel_settings' => 'shift_carousel_settings',
    ];

    /** be_groups columns that list CType values, tables or table:field pairs. */
    private const BE_GROUPS_FIELDS = ['explicit_allowdeny', 'non_exclude_fields', 'tables_select', 'tables_modify'];

    public function __construct(
        private ConnectionPool $connectionPool,
    ) {}

    public function getTitle(): string
    {
        return 'EXT:conn_t3wtk_elements: Rename "Flip Gallery"/"Flip Carousel" to "Lightbox Gallery"/"Shift Carousel"';
    }

    public function getDescription(): string
    {
        return 'Neither element flips anything (the name came from the GSAP Flip plugin), which '
            . 'clashed with the new 3D "Flip Card" element. Migrates tt_content records '
            . '(conn_flip_gallery => conn_lightbox_gallery, conn_flip_carousel => conn_shift_carousel), '
            . 'their tt_content columns, the carousel item table, its image file references and '
            . 'backend group permissions — preserving all data.';
    }

    public function getPrerequisites(): array
    {
        return [];
    }

    public function updateNecessary(): bool
    {
        return $this->countRecordsWithOldCType() > 0
            || $this->getTableActions() !== []
            || $this->getColumnActions() !== []
            || $this->getStaleFileReferenceTableNames() !== []
            || $this->countBackendGroupsToUpdate() > 0;
    }

    public function executeUpdate(): bool
    {
        // See RenameExtensionKeySchemaUpdate::executeUpdate() — structural ALTERs on a wide
        // tt_content are rejected with "Row size too large" under innodb_strict_mode.
        $connection = $this->connectionPool->getConnectionForTable('tt_content');
        $originalStrictMode = (string)$connection->executeQuery('SELECT @@SESSION.innodb_strict_mode')->fetchOne();
        $connection->executeStatement('SET SESSION innodb_strict_mode = 0');

        try {
            $this->migrateTables();
            $this->migrateTtContentColumns();
            $this->migrateFileReferences();
            $this->migrateCTypes();
            $this->migrateBackendGroups();
        } finally {
            $connection->executeStatement('SET SESSION innodb_strict_mode = ' . ($originalStrictMode !== '' ? (int)$originalStrictMode : 1));
        }

        return true;
    }

    // ─────────────────────────────────────────────────────────────
    // Old → new name pairs
    // ─────────────────────────────────────────────────────────────

    /**
     * @param array<string, string> $suffixMapping
     * @return list<array{old: string, new: string}>
     */
    private function expandPrefixes(array $suffixMapping): array
    {
        $pairs = [];
        foreach (self::OLD_PREFIXES as $oldPrefix) {
            foreach ($suffixMapping as $oldSuffix => $newSuffix) {
                $pairs[] = ['old' => $oldPrefix . $oldSuffix, 'new' => self::NEW_PREFIX . $newSuffix];
            }
        }

        return $pairs;
    }

    /**
     * Every renamed identifier (CTypes, tables, tt_content columns), old => new.
     *
     * @return array<string, string>
     */
    private function getAllReplacements(): array
    {
        $replacements = self::CTYPE_MAPPING;
        foreach ($this->expandPrefixes(self::TABLE_SUFFIX_MAPPING + self::TT_CONTENT_COLUMN_SUFFIX_MAPPING) as $pair) {
            $replacements[$pair['old']] = $pair['new'];
        }

        return $replacements;
    }

    // ─────────────────────────────────────────────────────────────
    // Tables
    // ─────────────────────────────────────────────────────────────

    /**
     * @return list<array{old: string, new: string, mode: 'rename'|'merge'|'conflict'}>
     */
    private function getTableActions(): array
    {
        $connection = $this->connectionPool->getConnectionForTable('tt_content');
        $existingTables = array_flip($connection->createSchemaManager()->listTableNames());

        $actions = [];
        $pendingRenames = [];
        foreach ($this->expandPrefixes(self::TABLE_SUFFIX_MAPPING) as $pair) {
            if (!isset($existingTables[$pair['old']])) {
                continue;
            }

            if (isset($pendingRenames[$pair['new']])) {
                // Both old-prefix variants exist and the other one is already being
                // renamed into place — can't tell which holds the real data.
                $actions[] = $pair + ['mode' => 'conflict'];
                continue;
            }

            if (!isset($existingTables[$pair['new']])) {
                $actions[] = $pair + ['mode' => 'rename'];
                $pendingRenames[$pair['new']] = true;
                continue;
            }

            $newRowCount = (int)$connection->executeQuery(sprintf(
                'SELECT COUNT(*) FROM %s',
                $connection->getDatabasePlatform()->quoteIdentifier($pair['new'])
            ))->fetchOne();

            $actions[] = $pair + ['mode' => $newRowCount === 0 ? 'merge' : 'conflict'];
        }

        return $actions;
    }

    private function migrateTables(): void
    {
        $connection = $this->connectionPool->getConnectionForTable('tt_content');
        $platform = $connection->getDatabasePlatform();

        foreach ($this->getTableActions() as $action) {
            if ($action['mode'] === 'conflict') {
                // Both exist and the new one already has rows of its own — leave both
                // alone rather than guess which data wins; needs a manual look.
                continue;
            }

            if ($action['mode'] === 'merge') {
                // New table was already created (empty) by a prior schema sync.
                $connection->executeStatement(sprintf('DROP TABLE %s', $platform->quoteIdentifier($action['new'])));
            }

            $connection->executeStatement(sprintf(
                'RENAME TABLE %s TO %s',
                $platform->quoteIdentifier($action['old']),
                $platform->quoteIdentifier($action['new'])
            ));
        }
    }

    // ─────────────────────────────────────────────────────────────
    // tt_content columns
    // ─────────────────────────────────────────────────────────────

    /**
     * @return list<array{old: string, new: string, mode: 'rename'|'merge'}>
     */
    private function getColumnActions(): array
    {
        $table = $this->connectionPool->getConnectionForTable('tt_content')
            ->createSchemaManager()
            ->introspectTable('tt_content');

        $actions = [];
        $taken = [];
        foreach ($this->expandPrefixes(self::TT_CONTENT_COLUMN_SUFFIX_MAPPING) as $pair) {
            if (!$table->hasColumn($pair['old'])) {
                continue;
            }
            $exists = $table->hasColumn($pair['new']) || isset($taken[$pair['new']]);
            $actions[] = $pair + ['mode' => $exists ? 'merge' : 'rename'];
            $taken[$pair['new']] = true;
        }

        return $actions;
    }

    private function migrateTtContentColumns(): void
    {
        $connection = $this->connectionPool->getConnectionForTable('tt_content');
        $platform = $connection->getDatabasePlatform();
        $table = $platform->quoteIdentifier('tt_content');

        foreach ($this->getColumnActions() as $action) {
            $old = $platform->quoteIdentifier($action['old']);
            $new = $platform->quoteIdentifier($action['new']);

            if ($action['mode'] === 'merge') {
                // New column was already created (empty/default) by a prior schema sync —
                // copy only the values the old column actually holds, then drop it.
                $connection->executeStatement(sprintf(
                    "UPDATE %s SET %s = %s WHERE %s IS NOT NULL AND %s NOT IN ('', '0')",
                    $table, $new, $old, $old, $old
                ));
                $connection->executeStatement(sprintf('ALTER TABLE %s DROP COLUMN %s', $table, $old));
                continue;
            }

            // Metadata-only rename, see RenameExtensionKeySchemaUpdate.
            $connection->executeStatement(sprintf('ALTER TABLE %s RENAME COLUMN %s TO %s', $table, $old, $new));
        }
    }

    // ─────────────────────────────────────────────────────────────
    // sys_file_reference
    // ─────────────────────────────────────────────────────────────

    /**
     * @return list<array{old: string, new: string}>
     */
    private function getStaleFileReferenceTableNames(): array
    {
        $connection = $this->connectionPool->getConnectionForTable('sys_file_reference');

        return array_values(array_filter(
            $this->expandPrefixes(self::TABLE_SUFFIX_MAPPING),
            static fn (array $pair): bool => $connection->count('uid', 'sys_file_reference', ['tablenames' => $pair['old']]) > 0
        ));
    }

    private function migrateFileReferences(): void
    {
        $connection = $this->connectionPool->getConnectionForTable('sys_file_reference');

        foreach ($this->getStaleFileReferenceTableNames() as $pair) {
            $connection->update('sys_file_reference', ['tablenames' => $pair['new']], ['tablenames' => $pair['old']]);
        }
    }

    // ─────────────────────────────────────────────────────────────
    // CType values
    // ─────────────────────────────────────────────────────────────

    private function countRecordsWithOldCType(): int
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('tt_content');
        $queryBuilder->getRestrictions()->removeAll();

        return (int)$queryBuilder
            ->count('uid')
            ->from('tt_content')
            ->where(
                $queryBuilder->expr()->in(
                    'CType',
                    $queryBuilder->createNamedParameter(array_keys(self::CTYPE_MAPPING), Connection::PARAM_STR_ARRAY)
                )
            )
            ->executeQuery()
            ->fetchOne();
    }

    private function migrateCTypes(): void
    {
        $connection = $this->connectionPool->getConnectionForTable('tt_content');

        foreach (self::CTYPE_MAPPING as $oldCType => $newCType) {
            $connection->update('tt_content', ['CType' => $newCType], ['CType' => $oldCType]);
        }
    }

    // ─────────────────────────────────────────────────────────────
    // be_groups permissions
    // ─────────────────────────────────────────────────────────────

    private function countBackendGroupsToUpdate(): int
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('be_groups');
        $queryBuilder->getRestrictions()->removeAll();

        $constraints = [];
        foreach (self::BE_GROUPS_FIELDS as $field) {
            foreach (array_keys($this->getAllReplacements()) as $old) {
                $constraints[] = $queryBuilder->expr()->like(
                    $field,
                    $queryBuilder->createNamedParameter('%' . $queryBuilder->escapeLikeWildcards($old) . '%')
                );
            }
        }

        return (int)$queryBuilder
            ->count('uid')
            ->from('be_groups')
            ->where($queryBuilder->expr()->or(...$constraints))
            ->executeQuery()
            ->fetchOne();
    }

    /**
     * Rewritten in PHP with strtr() (longest match wins, no chained re-replacement) rather
     * than nested SQL REPLACE(); a row is only written when something actually changed.
     */
    private function migrateBackendGroups(): void
    {
        $connection = $this->connectionPool->getConnectionForTable('be_groups');
        $replacements = $this->getAllReplacements();

        $rows = $connection->select(array_merge(['uid'], self::BE_GROUPS_FIELDS), 'be_groups')->fetchAllAssociative();
        foreach ($rows as $row) {
            $changes = [];
            foreach (self::BE_GROUPS_FIELDS as $field) {
                $value = (string)($row[$field] ?? '');
                $updated = strtr($value, $replacements);
                if ($updated !== $value) {
                    $changes[$field] = $updated;
                }
            }
            if ($changes !== []) {
                $connection->update('be_groups', $changes, ['uid' => (int)$row['uid']]);
            }
        }
    }
}
