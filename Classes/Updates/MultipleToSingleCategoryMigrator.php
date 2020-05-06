<?php
declare(strict_types=1);

namespace Tx\CzSimpleCal\Updates;

use Doctrine\DBAL\Driver\ResultStatement;
use Doctrine\DBAL\FetchMode;
use Exception;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Install\Updates\DatabaseUpdatedPrerequisite;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;

class MultipleToSingleCategoryMigrator implements UpgradeWizardInterface
{
    public function executeUpdate(): bool
    {
        $builder = $this->buildFirstCategoryQueryBuilder();
        $result = $builder->execute();
        $connection = $builder->getConnection();
        $connection->beginTransaction();
        try {
            $this->migrateCategories($result);
        } catch (Exception $e) {
            $connection->rollBack();
            throw $e;
        }

        $connection->commit();
        return true;
    }

    public function getDescription(): string
    {
        return 'Extract first MM category of event and set it as single category. Drop all MM categories.';
    }

    public function getIdentifier(): string
    {
        return 'tx-czsimplecal-multiple-to-single-category';
    }

    public function getPrerequisites(): array
    {
        return [DatabaseUpdatedPrerequisite::class];
    }

    public function getTitle(): string
    {
        return 'cz_simple_cal single category mode';
    }

    public function updateNecessary(): bool
    {
        $builder = $this->getQueryBuilderWithoutRestrictions('tx_czsimplecal_event_category_mm');
        $builder->count('uid');
        $builder->from('tx_czsimplecal_event_category_mm');
        return $builder->execute()->fetchColumn() > 0;
    }

    /**
     * @return QueryBuilder
     */
    private function buildFirstCategoryQueryBuilder(): QueryBuilder
    {
        $builder = $this->getQueryBuilderWithoutRestrictions('tx_czsimplecal_event_category_mm');
        $builder->select('mm.uid_local', 'mm.uid_foreign', 'mm.sorting');
        $builder->from('tx_czsimplecal_event_category_mm', 'mm');
        $builder->leftJoin(
            'mm',
            'tx_czsimplecal_event_category_mm',
            'mmInner',
            'mm.uid_local = mmInner.uid_local AND mm.sorting > mmInner.sorting'
        );
        $builder->where('mmInner.sorting IS NULL');
        return $builder;
    }

    private function getQueryBuilderWithoutRestrictions(string $table): QueryBuilder
    {
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $builder = $connectionPool->getQueryBuilderForTable($table);
        $builder->getRestrictions()->removeAll();
        return $builder;
    }

    /**
     * uid_local = event uid
     * uid_foreign = category uid
     *
     * @param ResultStatement $result
     */
    private function migrateCategories(ResultStatement $result): void
    {
        while ($row = $result->fetch(FetchMode::ASSOCIATIVE)) {
            // Set single category in related event.
            $this->getQueryBuilderWithoutRestrictions('tx_czsimplecal_domain_model_event')
                ->update('tx_czsimplecal_domain_model_event')
                ->set('category', $row['uid_foreign'])
                ->where('uid = ' . $row['uid_local'])
                ->execute();

            // Remove all mm records for related event.
            $this->getQueryBuilderWithoutRestrictions('tx_czsimplecal_event_category_mm')
                ->delete('tx_czsimplecal_event_category_mm')
                ->where('uid_local = ' . $row['uid_local'])
                ->execute();
        }
    }
}
