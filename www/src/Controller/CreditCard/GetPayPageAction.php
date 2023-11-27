<?php

declare(strict_types=1);

namespace App\Controller\CreditCard;

use App\Service\Invoice\InvoiceService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CompletePayAction
 * @package App\Controller\Api\CreditCard
 */
#[Route(path: '/account/pay-page/{invoiceId<\d+>}', name: 'pay-page', methods: ['GET'])]
class GetPayPageAction extends AbstractController
{
    private InvoiceService $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    /**
     * @param int $invoiceId
     * @return Response
     */
    public function __invoke(int $invoiceId): Response
    {
        try {
            $invoice = $this->invoiceService->getInvoiceById($invoiceId);
        } catch (\Throwable $e) {
            return new Response(null, Response::HTTP_NOT_FOUND);
        }

        return $this->render(
            'account/invoice/invoice-pay.html.twig',
            [
                'stripe_key' => $_ENV["STRIPE_KEY"],
                'invoice' => $invoice
            ]
        );
    }
}
