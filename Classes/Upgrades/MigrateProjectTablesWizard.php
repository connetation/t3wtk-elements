<?php

declare(strict_types=1);

namespace Connetation\Elements\Upgrades;

use Doctrine\DBAL\ParameterType;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Install\Attribute\UpgradeWizard;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;

// Replaces RenameProjectTableWizard, which left the old tables behind after
// copying. TYPO3 marks wizards as done by FQCN in sys_registry, so a new
// class name (not just a new identifier) is required to re-run on systems
// where the old wizard already completed.
#[UpgradeWizard('conn_elements_migrateProjectTables')]
final class MigrateProjectTablesWizard implements UpgradeWizardInterface
{
    private const RENAMES = [
        'tx_conn_elements_domain_model_project'         => 'tx_conn_elements_domain_model_dashboard_project',
        'tx_conn_elements_domain_model_projectcategory' => 'tx_conn_elements_domain_model_dashboard_projectcategory',
    ];

    public function getTitle(): string
    {
        return 'Conn Elements: Migrate project and category tables to dashboard scope';
    }

    public function getDescription(): string
    {
        return 'Moves data from the old project/category tables into the new dashboard-scoped tables, '
            . 'updates file references and drops the old tables afterwards.';
    }

    public function updateNecessary(): bool
    {
        foreach (self::RENAMES as $old => $_new) {
            if ($this->tableExists($old)) {
                return true;
            }
        }

        return $this->countStaleFileReferences() > 0;
    }

    public function executeUpdate(): bool
    {
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionByName(ConnectionPool::DEFAULT_CONNECTION_NAME);

        foreach (self::RENAMES as $old => $new) {
            if (!$this->tableExists($old)) {
                continue;
            }

            if (!$this->tableExists($new)) {
                $connection->executeStatement(
                    sprintf('RENAME TABLE `%s` TO `%s`', $old, $new)
                );
                continue;
            }

            if ($this->countRows($new) === 0 && $this->countRows($old) > 0) {
                $connection->executeStatement(
                    sprintf('INSERT INTO `%s` SELECT * FROM `%s`', $new, $old)
                );
            }

            // Either just copied or the new table already carries the data:
            // the old table is a stale leftover either way.
            $connection->executeStatement(
                sprintf('DROP TABLE `%s`', $old)
            );
        }

        $this->migrateFileReferences($connection);

        return true;
    }

    public function getPrerequisites(): array
    {
        return [];
    }

    private function migrateFileReferences(\TYPO3\CMS\Core\Database\Connection $connection): void
    {
        foreach (self::RENAMES as $old => $new) {
            $connection->executeStatement(
                'UPDATE `sys_file_reference` SET `tablenames` = ? WHERE `tablenames` = ?',
                [$new, $old],
                [ParameterType::STRING, ParameterType::STRING]
            );
        }
    }

    private function countStaleFileReferences(): int
    {
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionByName(ConnectionPool::DEFAULT_CONNECTION_NAME);

        $count = 0;
        foreach (self::RENAMES as $old => $_new) {
            $count += (int)$connection->executeQuery(
                'SELECT COUNT(*) FROM `sys_file_reference` WHERE `tablenames` = ?',
                [$old],
                [ParameterType::STRING]
            )->fetchOne();
        }

        return $count;
    }

    private function tableExists(string $table): bool
    {
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionByName(ConnectionPool::DEFAULT_CONNECTION_NAME);

        return in_array(
            $table,
            $connection->createSchemaManager()->listTableNames(),
            true
        );
    }

    private function countRows(string $table): int
    {
        if (!$this->tableExists($table)) {
            return 0;
        }

        $connection = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionByName(ConnectionPool::DEFAULT_CONNECTION_NAME);

        return (int)$connection->executeQuery(
            sprintf('SELECT COUNT(*) FROM `%s`', $table)
        )->fetchOne();
    }
}
