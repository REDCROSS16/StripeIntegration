<?php

declare(strict_types=1);

namespace App\Controller\PaySystem;

use App\Service\PaymentService\PaymentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class PayRecurrentStripeAction
 * @package App\Controller\PaySystem
 */
#[Route('/stripe/subscription', name: 'stripe-subscription-pay', methods: ['POST'])]
class PayRecurrentStripeAction extends AbstractController
{
    private PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function __invoke(Request $request)
    {
        try {
            $this->paymentService->subscribe($request, $this->getUser());
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
