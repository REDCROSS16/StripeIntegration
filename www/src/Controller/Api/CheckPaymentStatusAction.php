<?php

/**
 * Created by PhpStorm
 * User: red
 * Date: 27.11.2023
 * Time: 13:56
 * Project: StripeIntegration
 */

declare(strict_types=1);

namespace App\Controller\Api;

use App\Service\PaymentService\PaymentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CheckStatusOfPayment
 * @package App\Controller\Api\Invoice
 */
#[Route('/api/check-payment', name: 'api-check-payment', methods: ['POST'])]
class CheckPaymentStatusAction extends AbstractController
{
    private PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function __invoke(Request $request): Response
    {
        try {
            $payment = $this->paymentService->getPaymentById($request->request->get('paymentId'));
        } catch (\Throwable $e) {
            return $this->json(['status' => $e->getMessage(), Response::HTTP_BAD_REQUEST]);
        }

        return $this->json(['status' => $payment->getStatus()]);
    }
}
