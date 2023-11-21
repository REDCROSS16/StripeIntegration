<?php

/**
 * Created by PhpStorm
 * User: red
 * Date: 21.11.2023
 * Time: 11:41
 * Project: StripeIntegration
 */

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\DateManagementTrait;

/**
 * Class Payment
 * @package App\Entity
 */
class Payment
{
    use DateManagementTrait;

    private ?int $id = null;

    private string $status;
}