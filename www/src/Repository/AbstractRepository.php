<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\EntityInterface;
use App\Entity\SoftDeletableInterface;
use App\Repository\Exception\CannotBuildRepositoryException;
use App\Repository\Exception\FlushChangesException;
use App\Service\AuthManager;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class AbstractRepository
 * @package App\Repository
 */
abstract class AbstractRepository extends ServiceEntityRepository
{
    protected readonly LoggerInterface $logger;
    protected readonly TranslatorInterface $translator;
    protected readonly SecurityInterface $security;
    protected readonly EventDispatcherInterface $eventDispatcher;

    /**
     * @param ManagerRegistry $registry
     * @param LoggerInterface $logger
     * @param TranslatorInterface $translator
     * @param SecurityInterface $security
     * @param EventDispatcherInterface $eventDispatcher
     * @throws CannotBuildRepositoryException
     */
    public function __construct(
        ManagerRegistry $registry,
        LoggerInterface $logger,
        TranslatorInterface $translator,
        SecurityInterface $security,
        EventDispatcherInterface $eventDispatcher,
    ) {
        $this->translator = $translator;
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
            if ($soft === true && $entity instanceof SoftDeletableInterface) { //todo:
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
     * @return EntityManagerInterface
     */
    public function getEntityManager(): EntityManagerInterface
    {
        return parent::getEntityManager();
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
        } catch (Exception) {
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
        } catch (UniqueConstraintViolationException $ex) {
            preg_match_all('~duplicate entry ["\'](?P<value>.+)["\'].*for key ["\'](?P<key>.+)["\']~i', $ex->getMessage(), $matches);

            if (\count($matches['value'])) {
                throw new FlushChangesException($this->translator->trans('error.entity.non_unique_data', [
                    '{{ value }}' => $matches['value'][0],
                    '{{ field }}' => $matches['key'][0],
                ]));
            }

            throw new FlushChangesException($this->translator->trans('error.entity.non_unique_data_common'));
        } catch (\Exception) {
            throw new FlushChangesException($this->translator->trans('error.entity.save'));
        }
    }


}