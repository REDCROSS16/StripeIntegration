<?php

/**
 * Created by PhpStorm
 * User: red
 * Date: 27.11.2023
 * Time: 10:33
 * Project: StripeIntegration
 */

declare(strict_types=1);

namespace App\ENUM;

/**
 * Class Currency
 * @package App\ENUM
 */
enum Currency: string
{
    case USD = 'usd';
    case BYN = 'byn';
}