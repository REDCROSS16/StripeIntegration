<?php

declare(strict_types=1);

namespace App\Controller\PaySystem;

use App\Service\Invoice\InvoiceService;
use App\Service\PaymentService\PaymentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PayStripeAction
 * @package App\Controller\PaySystem
 */
#[Route('/stripe/pay', name: 'stripe-pay', methods: ['POST'])]
class PayStripeAction extends AbstractController
{
    private PaymentService $paymentService;

    public function __construct(PaymentService $paymentService, InvoiceService $invoiceService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function __invoke(Request $request): RedirectResponse
    {
        try {
            $this->paymentService->pay($request, $this->getUser());
        } catch (\Throwable $e) {
            $this->addFlash(
                'error',
                $e->getMessage()
            );

            return $this->redirectToRoute('payments-list', [], Response::HTTP_BAD_REQUEST);
        }

        $this->addFlash(
            'success',
            'Payment Successful!'
        );

        return $this->redirectToRoute('payments-list', [], Response::HTTP_OK);
    }
}
