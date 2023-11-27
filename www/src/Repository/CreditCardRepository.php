<?php

/**
 * Created by PhpStorm
 * User: red
 * Date: 21.11.2023
 * Time: 16:51
 * Project: StripeIntegration
 */

declare(strict_types=1);

namespace App\Repository;

use App\Entity\CreditCard;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class CreditCardRepository
 * @package App\Repository
 */
class CreditCardRepository extends AbstractRepository
{
    /**
     * @param int $userId
     * @return array
     */
    public function getAvailableCards(int $userId): array
    {
        $qb = $this->_em->createQueryBuilder();

        $qb->select('e')
            ->from(CreditCard::class, 'e')
            ->where('e.user = :userId')
            ->andWhere('e.isDeleted = :isDeleted')
            ->setParameter('isDeleted', false)
            ->setParameter('userId', $userId);

        return $this->getResult($qb);
    }

    /**
     * @param int $userId
     * @return int
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function countListOfActiveCard(int $userId): int
    {
        $qb = $this->_em->createQueryBuilder();

        $count = $qb->select('Count(e.id)')
            ->from(CreditCard::class, 'e')
            ->where('e.user = :userId')
            ->andWhere('e.isDeleted = :isDeleted')
            ->andWhere('e.isActive = :isActive')
            ->setParameter('isDeleted', false)
            ->setParameter('isActive', true)
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getSingleScalarResult();

        return (int) $count;
    }

    /**
     * @param string $token
     * @return CreditCard|null
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getCreditCardByToken(string $token): ?CreditCard
    {
        $qb = $this->_em->createQueryBuilder();

        $query = $qb->select('e')
            ->from(CreditCard::class, 'e')
            ->where('e.token = :token')
            ->andWhere('e.isDeleted = :isDeleted')
            ->andWhere('e.isActive = :isActive')
            ->setParameter('token', $token)
            ->setParameter('isDeleted', false)
            ->setParameter('isActive', true)
            ->setMaxResults(1)
            ->getQuery();

        return $query->getSingleResult();
    }

    /**
     * @param UserInterface $user
     * @return CreditCard
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getActiveCardByUser(UserInterface $user): CreditCard
    {
        $qb = $this->_em->createQueryBuilder();

        $query = $qb->select('e')
            ->from(CreditCard::class, 'e')
            ->where('e.user = :userId')
            ->andWhere('e.isDeleted = :isDeleted')
            ->andWhere('e.isActive = :isActive')
            ->setParameter('userId', $user->getId())
            ->setParameter('isDeleted', false)
            ->setParameter('isActive', true)
            ->setMaxResults(1)
            ->getQuery();

        return $query->getSingleResult();
    }
}
