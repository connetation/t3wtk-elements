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
 * A few content elements were shipped with a "conn_bs_*" (Bootstrap) CType prefix even
 * though they render no Bootstrap component. This wizard corrects existing tt_content
 * records to the renamed "conn_*" CType so they keep rendering after the code rename.
 *
 * Deliberately declares NO prerequisites (in particular not DatabaseUpdatedPrerequisite):
 * that prerequisite runs the standard "add missing tables/fields" schema sync as a side
 * effect of checking it, which — since other wizards in this package rename real tables/
 * columns — must not run before those wizards get a chance to migrate the old ones first.
 * This wizard only touches a value in an existing, unchanged column, so it never needed
 * that prerequisite anyway.
 */
#[UpgradeWizard('connElementsRenameBsPrefixedCTypes')]
readonly class RenameBsPrefixedCTypesUpdate implements UpgradeWizardInterface
{
    private const TABLE = 'tt_content';

    private const MAPPING = [
        'conn_bs_process' => 'conn_process',
        'conn_bs_before_after' => 'conn_before_after',
        'conn_bs_stats' => 'conn_stats',
        'conn_bs_logo_wall' => 'conn_logo_wall',
        'conn_bs_feature_grid' => 'conn_feature_grid',
        'conn_bs_timeline' => 'conn_timeline',
        'conn_bs_testimonials' => 'conn_testimonials',
    ];

    public function __construct(
        private ConnectionPool $connectionPool,
    ) {}

    public function getTitle(): string
    {
        return 'EXT:conn_t3wtk_elements: Rename misleading "conn_bs_*" CTypes to "conn_*"';
    }

    public function getDescription(): string
    {
        $mappingList = implode(', ', array_map(
            static fn (string $old, string $new): string => $old . ' => ' . $new,
            array_keys(self::MAPPING),
            array_values(self::MAPPING)
        ));

        return 'Several content elements were named with a "bs_" (Bootstrap) prefix even though '
            . 'they use no Bootstrap component. This updates existing tt_content records to the '
            . 'corrected CType: ' . $mappingList;
    }

    public function getPrerequisites(): array
    {
        return [];
    }

    public function updateNecessary(): bool
    {
        return $this->countRecordsToUpdate() > 0;
    }

    public function executeUpdate(): bool
    {
        $connection = $this->connectionPool->getConnectionForTable(self::TABLE);

        foreach (self::MAPPING as $oldCType => $newCType) {
            $connection->update(
                self::TABLE,
                ['CType' => $newCType],
                ['CType' => $oldCType]
            );
        }

        return true;
    }

    private function countRecordsToUpdate(): int
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::TABLE);
        $queryBuilder->getRestrictions()->removeAll();

        return (int)$queryBuilder
            ->count('uid')
            ->from(self::TABLE)
            ->where(
                $queryBuilder->expr()->in(
                    'CType',
                    $queryBuilder->createNamedParameter(array_keys(self::MAPPING), Connection::PARAM_STR_ARRAY)
                )
            )
            ->executeQuery()
            ->fetchOne();
    }
}
