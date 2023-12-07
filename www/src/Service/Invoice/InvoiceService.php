<?php

/**
 * Created by PhpStorm
 * User: red
 * Date: 26.11.2023
 * Time: 22:56
 * Project: StripeIntegration
 */

declare(strict_types=1);

namespace App\Service\Invoice;

use App\Entity\Invoice;
use App\ENUM\InvoiceStatus;
use App\Service\Datetime\DateTimeWrapper;
use App\Service\Validator\InvoiceValidator;
use App\Utils\Converter\DataConverter;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use mysql_xdevapi\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class InvoiceService
 * @package App\Service\Invoice
 */
class InvoiceService
{
    private EntityManagerInterface $entityManager;
    private InvoiceValidator $validator;

    public function __construct(EntityManagerInterface $entityManager, InvoiceValidator $validator)
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    /**
     * @param Request $request
     * @return Invoice|null
     * @throws \Exception
     */
    public function handleRequest(Request $request): ?Invoice
    {
        [$user, $order, $amount, $description] = $this->validator->validate($request);

        /** @var Invoice $invoice */
        $invoice = $this->getInvoiceRepo()->getFreshEntity();

        $invoice
            ->setOrder($order)
            ->setAmount($amount)
            ->setUser($user->getId())
            ->setStatus(InvoiceStatus::PENDING->value)
            ->setSignature(md5(DataConverter::toString(DateTimeWrapper::getTimestamp())))
            ->setDescription($description);

        $this->getInvoiceRepo()->save($invoice);

        return $invoice;
    }

    /**
     * @return EntityRepository
     */
    public function getInvoiceRepo(): EntityRepository
    {
        return $this->entityManager->getRepository(Invoice::class);
    }

    /**
     * @param UserInterface $user
     * @return array|null
     */
    public function getInvoicesList(UserInterface $user): ?array
    {
        return $this->getInvoiceRepo()->getInvoicesList($user->getId());
    }

    /**
     * @param int|null $invoiceId
     * @return Invoice|null
     */
    public function getInvoiceById(?int $invoiceId): ?Invoice
    {
        return $this->getInvoiceRepo()->findOneBy(['id'=>$invoiceId]);
    }
}
