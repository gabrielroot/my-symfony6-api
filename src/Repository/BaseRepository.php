<?php

namespace App\Repository;

use App\Interface\IAudit;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<IAudit>
 */
abstract class BaseRepository extends ServiceEntityRepository
{
    protected $entity;

    public function __construct(ManagerRegistry $registry, $className)
    {
        parent::__construct($registry, $className);

        $this->entity = new $className;
    }

    public function save(IAudit $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function deleteNow(IAudit $entity, bool $flush = true): void
    {
        $entity->setDeletedAt(new DateTime());
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function reactivate(IAudit $entity, bool $flush = true): void
    {
        $entity->setDeletedAt(null);
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function find(mixed $id, mixed $lockMode = null, $lockVersion = null, bool $onlyActive = true): ?IAudit
    {
        $qb = $this->newCriteriaActiveQb(onlyActive: $onlyActive);

        return $qb
            ->andWhere($qb->expr()->eq('entity.id', ':id'))
            ->setParameter(key: 'id', value: $id)
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult();
    }

    public function findOneBy(array $criteria, array $orderBy = null, bool $onlyActive = true): ?IAudit
    {
        $qb = $this->newCriteriaActiveQb(onlyActive: $onlyActive);

        $this->addCriteriaAndOrder($qb, $criteria, $orderBy ?? []);

        return $qb
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult();
    }

    public function findBy(
        array $criteria,
        array $orderBy = null,
        ?int $limit = null,
        ?int $offset = 0,
        bool $onlyActive = true): array
    {
        $qb = $this->newCriteriaActiveQb(onlyActive: $onlyActive);

        $this->addCriteriaAndOrder($qb, $criteria, $orderBy ?? []);

        return $qb
            ->getQuery()
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getResult();
    }

    public function findAll(): array
    {
        return $this
            ->newCriteriaActiveQb()
            ->getQuery()
            ->getResult();
    }

    public function queryAll(array $criteria = [], $onlyActive = true, array $orderBy = []): Query
    {
        $qb = $this->newCriteriaActiveQb($onlyActive);
        $this->addCriteriaAndOrder($qb, $criteria, $orderBy);

        return $qb->getQuery();
    }

    public function newCriteriaActiveQb(bool $onlyActive = true): QueryBuilder
    {
        $qb = $this->createQueryBuilder('entity');

        if ($onlyActive) {
            $qb->where($qb->expr()->isNull('entity.deletedAt'));
        }

        return $qb;
    }

    private function addCriteriaAndOrder(QueryBuilder $qb, array $criteria, array $orderBy): void
    {
        foreach ($criteria as $column => $value) {
            if (is_null($value)) {
                $qb->andWhere($qb->expr()->isNull("entity.$column"));
                continue;
            }

            $qb
                ->andWhere("entity.$column = :$column")
                ->setParameter($column, $value);
        }

        foreach ($orderBy as $column => $value) {
            $qb->addOrderBy("entity.$column", $value);
        }
    }

    public function getOneRandom(bool $onlyActives = false): mixed
    {
        return $this->newCriteriaActiveQb(false)
            ->select('DISTINCT entity')
            ->orderBy('RAND()', 'ASC')
            ->getQuery()
            ->setMaxResults(1)
            ->getSingleResult();
    }
}