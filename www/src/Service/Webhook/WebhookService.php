<?php

/**
 * Created by PhpStorm
 * User: red
 * Date: 29.11.2023
 * Time: 00:37
 * Project: StripeIntegration
 */

declare(strict_types=1);

namespace App\Service\Webhook;

use Symfony\Component\HttpFoundation\Request;

/**
 * Class WebhookService
 * @package App\Service\Webhook
 */
class WebhookService
{
    public function handle(Request $request)
    {
        // mapped information from stripe response
        // TODO:
    }
}
