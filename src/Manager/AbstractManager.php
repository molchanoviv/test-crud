<?php

declare(strict_types=1);

namespace App\Manager;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Persistence\Mapping\MappingException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\ORMInvalidArgumentException;

/**
 * App\Manager\AbstractManager.
 */
abstract class AbstractManager
{
    /**
     * @var EntityManagerInterface
     */
    protected EntityManagerInterface $entityManager;

    abstract public function getEntityClassName(): string;

    /**
     * @return object
     */
    public function createNew()
    {
        $entityClassName = $this->getEntityClassName();

        return new $entityClassName();
    }

    /**
     * @param object $entity
     *
     * @throws ORMInvalidArgumentException
     * @throws ORMException
     */
    public function refresh($entity): void
    {
        $this->getEntityManager()->refresh($entity);
    }

    /**
     * @param object $entity
     *
     * @throws ORMInvalidArgumentException
     * @throws ORMException
     */
    public function remove($entity): void
    {
        $this->getEntityManager()->remove($entity);
    }

    /**
     * @param object $entity
     *
     * @throws ORMInvalidArgumentException
     * @throws ORMException
     */
    public function persist($entity): void
    {
        $this->getEntityManager()->persist($entity);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    /**
     * @param object $entity
     *
     * @throws OptimisticLockException
     * @throws ORMInvalidArgumentException
     * @throws ORMException
     */
    public function save($entity): void
    {
        $this->persist($entity);
        $this->flush();
    }

    /**
     * @throws ORMInvalidArgumentException
     * @throws MappingException
     */
    public function clear(?string $entityName = null): void
    {
        $this->getEntityManager()->clear($entityName);
    }

    /**
     * @return object|null
     */
    public function find(int $id, ?int $lockMode = null, ?int $lockVersion = null)
    {
        return $this->getEntityRepository()->find($id, $lockMode, $lockVersion);
    }

    /**
     * @return array
     */
    public function findAll(): iterable
    {
        return $this->getEntityRepository()->findAll();
    }

    /** @noinspection MoreThanThreeArgumentsInspection */
    /**
     * @return array
     */
    public function findBy(array $criteria, ?array $orderBy = null, ?int $limit = null, ?int $offset = null): iterable
    {
        return $this->getEntityRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @return object|null
     */
    public function findOneBy(array $criteria, ?array $orderBy = null)
    {
        return $this->getEntityRepository()->findOneBy($criteria, $orderBy);
    }

    public function matching(Criteria $criteria): Collection
    {
        return $this->getEntityRepository()->matching($criteria);
    }

    public function getClassMetaData(): ClassMetadata
    {
        return $this->getEntityManager()->getClassMetadata($this->getEntityClassName());
    }

    protected function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    /**
     * @param EntityManagerInterface $entityManager
     *
     * @return AbstractManager
     */
    public function setEntityManager(EntityManagerInterface $entityManager): self
    {
        $this->entityManager = $entityManager;

        return $this;
    }

    /**
     * Return repository of entity handled by the userManager.
     *
     * @return EntityRepository
     */
    public function getEntityRepository()
    {
        /** @var EntityRepository $repository */
        $repository = $this->getEntityManager()->getRepository($this->getEntityClassName());

        return $repository;
    }
}
