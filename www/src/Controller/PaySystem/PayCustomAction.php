<?php

/**
 * Created by PhpStorm
 * User: red
 * Date: 26.11.2023
 * Time: 22:34
 * Project: StripeIntegration
 */

declare(strict_types=1);

namespace App\Controller\PaySystem;

use App\Service\PaymentService\PaymentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CompletePayAction
 * @package App\Controller\CreditCard
 */
#[Route('/account/custom-pay', name: 'custom-pay', methods: ['POST'])]
class PayCustomAction extends AbstractController
{
    private PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function __invoke(Request $request): Response
    {
        try {
            $this->paymentService->customPay();
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
