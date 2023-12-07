<?php

/**
 * Created by PhpStorm
 * User: red
 * Date: 27.11.2023
 * Time: 14:50
 * Project: StripeIntegration
 */

declare(strict_types=1);

namespace App\Controller\Api;

use App\Service\Invoice\InvoiceService;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CancelStripeSubscriptionAction
 * @package App\Controller\Api\Invoice
 */
#[Route('/api/stripe/cancel/{invoice}', name: 'api-stripe-cancel', methods: ['POST'])]
class CancelStripeSubscriptionAction extends AbstractController
{
    private InvoiceService $invoiceService;
    private StripeClient $stripe;

    /**
     * Constructor CancelStripeSubscriptionAction
     */
    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
        $this->stripe = new StripeClient($_ENV["STRIPE_SECRET"]);
    }

    /**
     * @param int $invoice
     * @return JsonResponse
     * @throws ApiErrorException
     */
    public function __invoke(int $invoice): JsonResponse
    {
        $invoice = $this->invoiceService->getInvoiceById($invoice);
        $result = $this->stripe->subscriptions->cancel($invoice->getData()['id'], []);

        return $this->json($result->jsonSerialize());
    }
}
