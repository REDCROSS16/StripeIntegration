<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\EntityInterface;
use App\Entity\Traits\SoftDeletableInterface;
use App\Repository\Exception\CannotBuildRepositoryException;
use App\Repository\Exception\FlushChangesException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class AbstractRepository
 * @package App\Repository
 */
abstract class AbstractRepository extends ServiceEntityRepository
{
    protected readonly LoggerInterface $logger;
    protected readonly Security $security;
    protected readonly EventDispatcherInterface $eventDispatcher;

    /**
     * @param ManagerRegistry $registry
     * @param LoggerInterface $logger
     * @param Security $security
     * @param EventDispatcherInterface $eventDispatcher
     * @throws CannotBuildRepositoryException
     */
    public function __construct(
        ManagerRegistry $registry,
        LoggerInterface $logger,
        Security $security,
        EventDispatcherInterface $eventDispatcher,
    ) {
        $this->logger = $logger;
        $this->security = $security;
        $this->eventDispatcher = $eventDispatcher;

        if (!class_exists($entityClassName = $this->getEntityClassName())) {
            throw new CannotBuildRepositoryException(" Entity \"{$entityClassName}\" does not exist");
        }

        parent::__construct($registry, $entityClassName);
    }

    /**
     * @param EntityInterface $entity
     * @param bool $soft
     * @return void
     * @throws FlushChangesException
     */
    public function delete(EntityInterface $entity, bool $soft = true): void
    {
        try {
            if ($soft === true && $entity instanceof SoftDeletableInterface) {
                $entity->setIsDeleted(true);
                $entity->setIsActive(false);
                $this->_em->persist($entity);
            } else {
                $this->_em->remove($entity);
            }
            $this->_em->flush();
        } catch (\Exception) {
            throw new FlushChangesException($this->translator->trans('error.entity.delete'));
        }
    }

    /**
     * @return EntityInterface
     */
    public function getFreshEntity(): EntityInterface
    {
        return new $this->_entityName();
    }

    /**
     * @param SoftDeletableInterface $entity
     * @return void
     * @throws FlushChangesException
     */
    public function recover(SoftDeletableInterface $entity): void
    {
        $entity->setIsDeleted(false);

        try {
            $this->_em->persist($entity);
            $this->_em->flush();
        } catch (\Exception) {
            throw new FlushChangesException($this->translator->trans('error.entity.recover'));
        }
    }

    /**
     * @param EntityInterface $entity
     * @return void
     * @throws FlushChangesException
     */
    public function save(EntityInterface $entity): void
    {
        try {
            $this->_em->persist($entity);
            $this->_em->flush();
        } catch (\Exception $e) {
            throw new FlushChangesException($e->getMessage());
        }
    }

    /**
     * @param QueryBuilder $qb
     * @param bool $useCache
     * @return Query
     */
    protected function getQuery(QueryBuilder $qb, bool $useCache = false): Query
    {
        $query = $qb->getQuery();

        return $useCache ? $query->enableResultCache() : $query->disableResultCache();
    }

    /**
     * @return string
     * @throws CannotBuildRepositoryException
     */
    protected function getEntityClassName(): string
    {
        $repositoryNameParts = explode('\\', \get_class($this));
        $repositoryName = end($repositoryNameParts);

        if (!str_contains($repositoryName, 'Repository')) {
            throw new CannotBuildRepositoryException(' Non-standard repository name given. Repository must be named like "EntityNameRepository"');
        }

        return 'App\Entity\\' . str_replace('Repository', '', $repositoryName);
    }

    /**
     * @param QueryBuilder $qb
     * @param bool $useCache
     * @return array
     */
    protected function getResult(QueryBuilder $qb, bool $useCache = false): array
    {
        try {
            $result = $this->getQuery($qb, $useCache)->getResult();
        } catch (\Exception $ex) {
            $this->logger->error($ex->getFile() . "({$ex->getLine()}): {$ex->getMessage()}");
            $result = [];
        }

        return $result;
    }
}
