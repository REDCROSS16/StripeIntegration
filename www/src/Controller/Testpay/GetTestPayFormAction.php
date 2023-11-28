<?php

/**
 * Created by PhpStorm
 * User: red
 * Date: 28.11.2023
 * Time: 14:59
 * Project: StripeIntegration
 */

declare(strict_types=1);

namespace App\Controller\Testpay;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class GetTestPayFormAction
 * @package App\Controller\Testpay
 * @author red <zvertred@gmail.com>
 */
#[Route('/test/form', name: 'test-form', methods: ['GET'])]
class GetTestPayFormAction extends AbstractController
{
    public function __invoke(): Response
    {
        return $this->render(
            'test-pay/test-pay.html.twig',
            [
                'stripe_key' => $_ENV["STRIPE_KEY"],
            ]
        );
    }
}
