<?php

/**
 * Created by PhpStorm
 * User: red
 * Date: 27.11.2023
 * Time: 13:42
 * Project: StripeIntegration
 */

declare(strict_types=1);

namespace App\ENUM;

/**
 * Class SubscriptionPlan
 * @package App\ENUM
 */
enum SubscriptionPlan: string
{
    case PLAN_WEEK = 'week';
    case PLAN_MONTH = 'month';
    case PLAN_YEAR = 'year';

    public function getSubscriptionPlan(): array
    {
        return match ($this) {
            self::PLAN_WEEK => [
                'name' => 'Week description',
//                'price' => $this->dayPrice * 7,
                'interval' => self::PLAN_WEEK
            ],
            self::PLAN_MONTH => [
                'name' => 'Month description',
//                'price' => $this->dayPrice * 30,
                'interval' => self::PLAN_MONTH
            ],
            self::PLAN_YEAR => [
                'name' => 'Year description',
//                'price' => $this->dayPrice * 365,
                'interval' => self::PLAN_YEAR
            ],
        };
    }
}
