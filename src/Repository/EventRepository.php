<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Sulu\Component\SmartContent\Orm\DataProviderRepositoryInterface;
use Sulu\Component\SmartContent\Orm\DataProviderRepositoryTrait;

/**
 * @extends ServiceEntityRepository<Event>
 */
class EventRepository extends ServiceEntityRepository implements DataProviderRepositoryInterface
{
    use DataProviderRepositoryTrait {
        findByFilters as parentFindByFilters;
    }

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    public function create(string $locale): Event
    {
        $event = new Event();
        $event->setLocale($locale);

        return $event;
    }

    public function remove(int $id): void
    {
        /** @var object $event */
        $event = $this->getEntityManager()->getReference(
            $this->getClassName(),
            $id,
        );

        $this->getEntityManager()->remove($event);
        $this->getEntityManager()->flush();
    }

    public function save(Event $event): void
    {
        $this->getEntityManager()->persist($event);
        $this->getEntityManager()->flush();
    }

    public function findById(int $id, string $locale): ?Event
    {
        $event = $this->find($id);
        if (!$event instanceof Event) {
            return null;
        }

        $event->setLocale($locale);

        return $event;
    }

    /**
     * @param mixed[] $filters
     */
    public function findByFilters($filters, $page, $pageSize, $limit, $locale, $options = [])
    {
        $entities = $this->parentFindByFilters($filters, $page, $pageSize, $limit, $locale, $options);

        return \array_map(
            fn (Event $entity) => $entity->setLocale($locale),
            $entities,
        );
    }

    /**
     * @param string $alias
     * @param string $locale
     *
     * @return void
     */
    protected function appendJoins(QueryBuilder $queryBuilder, $alias, $locale)
    {
        // join and select entities that are used for creating data items or resource items in the DataProvider here
    }

    /**
     * @param mixed[] $options
     *
     * @return string[]
     */
    protected function append(QueryBuilder $queryBuilder, string $alias, string $locale, $options = []): array
    {
        $queryBuilder->andWhere($alias . '.enabled = true');

        return [];
    }

    protected function appendSortByJoins(QueryBuilder $queryBuilder, string $alias, string $locale): void
    {
        $queryBuilder->innerJoin($alias . '.translations', 'translation', Join::WITH, 'translation.locale = :locale');
        $queryBuilder->setParameter('locale', $locale);
    }
}
