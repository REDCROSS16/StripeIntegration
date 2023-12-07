<?php

/**
 * Created by PhpStorm
 * User: red
 * Date: 28.11.2023
 * Time: 14:59
 * Project: StripeIntegration
 */

declare(strict_types=1);

namespace App\Controller\StripePay;

use App\Service\Invoice\InvoiceService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class GetTestPayFormAction
 * @package App\Controller\StipePay
 */
#[Route('/stripe/form/{invoiceId}', name: 'stripe-form', methods: ['GET'])]
class GetStripePayFormAction extends AbstractController
{
    private InvoiceService $invoiceService;
    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    public function __invoke(int $invoiceId): Response
    {
        return $this->render(
            'stripe-pay/stripe-pay.html.twig',
            [
                'stripe_key' => $_ENV["STRIPE_KEY"],
                'user'       => $this->getUser(),
                'invoice'    => $this->invoiceService->getInvoiceById($invoiceId)
            ]
        );
    }
}
