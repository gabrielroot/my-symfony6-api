<?php

namespace App\Service;

use App\Interface\IAudit;
use App\Repository\BaseRepository;

abstract class AbstractService
{
    private baseRepository $repository;

    public function __construct(
        BaseRepository $repository)
    {
        $this->repository = $repository;
    }
    public function save(IAudit $entity, bool $flush = true): void
    {
        $this->repository->save($entity, $flush);
    }

    public function deleteNow(IAudit $entity, bool $flush = true): void
    {
        $this->repository->deleteNow($entity, $flush);
    }

    public function reactivate(IAudit $entity, bool $flush = true): void
    {
        $this->repository->reactivate($entity, $flush);
    }
    public function find(mixed $id, mixed $lockMode = null, $lockVersion = null, bool $onlyActive = true)
    {
        return $this->repository->find($id, $lockMode, $lockVersion, $onlyActive);
    }

    public function findAll(): array
    {
        return $this->repository->findAll();
    }

    public function findBy(
        array $criteria,
        array $orderBy = null,
        ?int $limit = null,
        ?int $offset = 0,
        bool $onlyActive = true): array
    {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset, $onlyActive);
    }

    public function findOneBy(
        array $criteria,
        array $orderBy = null,
        ?int $limit = null,
        ?int $offset = 0,
        bool $onlyActive = true)
    {
        return $this->repository->findOneBy($criteria, $orderBy, $onlyActive);
    }
}