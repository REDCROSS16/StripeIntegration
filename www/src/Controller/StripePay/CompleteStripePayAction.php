<?php

/**
 * Created by PhpStorm
 * User: red
 * Date: 28.11.2023
 * Time: 18:43
 * Project: StripeIntegration
 */

declare(strict_types=1);

namespace App\Controller\StripePay;

use App\Service\PaymentService\PaymentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CompletePayTestAction
 * @package App\Controller\StripePay
 */
#[Route('/stripe/pay', name: 'stripe-pay', methods: ['POST'])]
class CompleteStripePayAction extends AbstractController
{
    private PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function __invoke(Request $request): Response
    {
        try {
            $this->paymentService->simplePay($request, $this->getUser());
        } catch (\Exception $e) {
            return $this->redirectToRoute('account');
        }

        return $this->redirectToRoute('account');
    }
}
