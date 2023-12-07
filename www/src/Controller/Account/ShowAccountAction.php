<?php

/**
 * Created by PhpStorm
 * User: red
 * Date: 23.11.2023
 * Time: 12:07
 * Project: StripeIntegration
 */

declare(strict_types=1);

namespace App\Controller\Account;

use App\Service\PaymentService\PaymentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ShowAccountAction
 * @package App\Controller\Api\User
 */
#[Route('/account', name: 'account', methods: ['GET', 'POST'])]
class ShowAccountAction extends AbstractController
{
    private PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function __invoke(Request $request): Response
    {
        return $this->render(
            'account/payment/payment.html.twig',
            [
                'payments'  => $this->paymentService->getPaymentsList($this->getUser()),
                'user' => $this->getUser()
            ]
        );
    }
}
