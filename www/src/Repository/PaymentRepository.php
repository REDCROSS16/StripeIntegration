<?php

/**
 * Created by PhpStorm
 * User: red
 * Date: 21.11.2023
 * Time: 16:08
 * Project: StripeIntegration
 */

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Payment;

/**
 * Class PaymentRepository
 * @package App\Repository
 */
class PaymentRepository extends AbstractRepository
{
    /**
     * @param int $userId
     * @return array
     */
    public function getPaymentsList(int $userId): array
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('e')
            ->from(Payment::class, 'e')
            ->where('e.user = :userId')
            ->andWhere('e.isDeleted = :isDeleted')
            ->andWhere('e.isActive = :isActive')
            ->setParameter('userId', $userId)
            ->setParameter('isDeleted', false)
            ->setParameter('isActive', true);

        return $this->getResult($qb);
    }
}
