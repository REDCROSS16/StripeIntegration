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

use App\Service\Webhook\WebhookService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class GetWebhookAction
 * @package App\Controller\Api
 */
#[Route(path: '/api/stripe/webhook', name: 'api-stripe-webhook', methods: ['POST'])]
class GetWebhookAction extends AbstractController
{
    private WebhookService $webhookService;
    public function __construct(WebhookService $webhookService)
    {
        $this->webhookService = $webhookService;
    }

    public function __invoke(Request $request): void
    {
        $this->webhookService->handle($request);
    }
}
