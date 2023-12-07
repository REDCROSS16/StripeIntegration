<?php

/**
 * Created by PhpStorm
 * User: red
 * Date: 26.11.2023
 * Time: 15:47
 * Project: StripeIntegration
 */

declare(strict_types=1);

namespace App\Controller\Payment;

use App\Entity\Payment;
use App\Service\PaymentService\PaymentService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class GetPaymentsAction
 * @package App\Controller\Payment
 */
#[Route(path: '/account/payments/list', name: 'payments-list', methods: ['GET'])]
class GetPaymentsAction extends AbstractController
{
    private PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function __invoke(): Response
    {
        return $this->render(
            'account/payment/payment.html.twig',
            [
                'payments'   => $this->paymentService->getPaymentsList($this->getUser())
            ]
        );
    }
}
