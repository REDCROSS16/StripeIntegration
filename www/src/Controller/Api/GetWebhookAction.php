<?php

/**
 * Created by PhpStorm
 * User: red
 * Date: 27.11.2023
 * Time: 15:50
 * Project: StripeIntegration
 */

declare(strict_types=1);

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class GetWebhookAction
 * @package App\Controller\Api
 */
#[Route('/api/stripe/webhook', name: 'api-stripe-webhook', methods: ['POST'])]
class GetWebhookAction extends AbstractController
{
    public function __invoke(Request $request)
    {
        // TODO: обработать вебхук
        // HandleService::handleWebhook($request);
    }
}
