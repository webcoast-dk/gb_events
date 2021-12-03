<?php

declare(strict_types=1);

namespace GuteBotschafter\GbEvents\Update;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\DataHandling\SlugHelper;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Install\Updates\DatabaseUpdatedPrerequisite;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;

/**
 * Migrate empty slugs
 */
class EventsSlugUpdater implements UpgradeWizardInterface
{
    protected const TABLE = 'tx_gbevents_domain_model_event';

    public function executeUpdate(): bool
    {
        $fieldConfig = $GLOBALS['TCA'][static::TABLE]['columns']['url_segment']['config'];
        $slugHelper = GeneralUtility::makeInstance(SlugHelper::class, static::TABLE, 'url_segment', $fieldConfig);

        $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable(static::TABLE);
        $queryBuilder = $connection->createQueryBuilder();
        $queryBuilder->getRestrictions()->removeAll();
        $statement = $queryBuilder->select('*')
            ->from(static::TABLE)
            ->where(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->eq('url_segment', $queryBuilder->createNamedParameter('')),
                    $queryBuilder->expr()->isNull('url_segment')
                )
            )
            ->execute();
        $updateQueryBuilder = $connection->createQueryBuilder();
        $updateQueryBuilder->update(static::TABLE)
            ->where(
                $updateQueryBuilder->expr()->eq(
                    'uid',
                    $updateQueryBuilder->createNamedParameter(0, \PDO::PARAM_INT, ':uid')
                )
            )
            ->set('url_segment', $updateQueryBuilder->createNamedParameter('', \PDO::PARAM_STR, ':url_segment'), false);
        while ($record = $statement->fetch()) {
            if ((string)$record['title'] !== '') {
                $slug = $slugHelper->generate($record, $record['pid']);
                $updateQueryBuilder->setParameter('uid', $record['uid'])
                    ->setParameter('url_segment', $this->getUniqueValue($record['uid'], $slug));
                $updateQueryBuilder->execute();
            }
        }

        return true;
    }

    public function updateNecessary(): bool
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable(static::TABLE);
        $queryBuilder->getRestrictions()->removeAll();
        return $queryBuilder->count('uid')
            ->from(static::TABLE)
            ->where(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->eq('url_segment', $queryBuilder->createNamedParameter('')),
                    $queryBuilder->expr()->isNull('url_segment')
                )
            )
            ->execute()->fetchColumn() > 0;
    }

    /**
     * @param int $uid
     * @param string $slug
     * @return string
     */
    protected function getUniqueValue(int $uid, string $slug): string
    {
        $statement = $this->getUniqueCountStatement($uid, $slug);
        if ($statement->fetchColumn()) {
            for ($counter = 1; $counter <= 100; $counter++) {
                $newSlug = $slug . '-' . $counter;
                $statement->bindValue(1, $newSlug);
                $statement->execute();
                if (!$statement->fetchColumn()) {
                    break;
                }
            }
        }

        return $newSlug ?? $slug;
    }

    /**
     * @param int $uid
     * @param string $slug
     * @return \Doctrine\DBAL\Driver\Statement|int
     */
    protected function getUniqueCountStatement(int $uid, string $slug)
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable(static::TABLE);
        /** @var DeletedRestriction $deleteRestriction */
        $deleteRestriction = GeneralUtility::makeInstance(DeletedRestriction::class);
        $queryBuilder->getRestrictions()->removeAll()->add($deleteRestriction);

        return $queryBuilder
            ->count('uid')
            ->from(static::TABLE)
            ->where(
                $queryBuilder->expr()->eq(
                    'url_segment',
                    $queryBuilder->createPositionalParameter($slug)
                ),
                $queryBuilder->expr()->neq('uid', $queryBuilder->createPositionalParameter($uid, \PDO::PARAM_INT))
            )->execute();
    }

    public function getPrerequisites(): array
    {
        return [
            DatabaseUpdatedPrerequisite::class
        ];
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle(): string
    {
        return 'Updates slug field of EXT:gb_events records';
    }

    /**
     * Get description
     *
     * @return string Longer description of this updater
     */
    public function getDescription(): string
    {
        return 'Fills empty slug field of EXT:gb_events records with urlized title.';
    }

    /**
     * @return string Unique identifier of this updater
     */
    public function getIdentifier(): string
    {
        return __CLASS__;
    }
}
