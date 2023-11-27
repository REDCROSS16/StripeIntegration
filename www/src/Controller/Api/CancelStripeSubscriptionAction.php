<?php

/**
 * Created by PhpStorm
 * User: red
 * Date: 27.11.2023
 * Time: 14:50
 * Project: StripeIntegration
 */

declare(strict_types=1);

namespace App\Controller\Api;

use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CancelStripeSubscriptionAction
 * @package App\Controller\Api\Invoice
 */
#[Route('/api/stripe/cancel', name: 'api-stripe-cancel', methods: ['POST'])]
class CancelStripeSubscriptionAction
{

    /**
     * Constructor CancelStripeSubscriptionAction
     */
    public function __construct()
    {
    }

    public function __invoke()
    {
        //todo:
    }
}
