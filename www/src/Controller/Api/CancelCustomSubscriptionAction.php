<?php

/**
 * Created by PhpStorm
 * User: red
 * Date: 27.11.2023
 * Time: 15:03
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
 * Class CancelCustomSubscriptionAction
 * @package App\Controller\Api\Invoice
 */
#[Route('/api/custom/cancel', name: 'api-custom-cancel', methods: ['POST'])]
class CancelCustomSubscriptionAction extends AbstractController
{
    private PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function __invoke(Request $request)
    {
        try {
            $this->paymentService->cancelCustomSubscription($request, $this->getUser());
        } catch (\Throwable $e) {
            return $this->json($e->getMessage(), Response::HTTP_BAD_REQUEST);
        }

        return $this->json('subscription successfully cancelled', Response::HTTP_OK);
    }
}