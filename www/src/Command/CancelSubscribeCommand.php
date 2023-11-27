<?php

/**
 * Created by PhpStorm
 * User: red
 * Date: 27.11.2023
 * Time: 14:30
 * Project: StripeIntegration
 */

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Command\Command;

/**
 * Class CancelSubscribeCommand
 * @package App\Command
 */
class CancelSubscribeCommand extends Command
{

    // TODO: проверять время подписки, если дата меньше чем сегодня отменять подписку
    // и отправлять уведомление (добавить транспорт или мейлер)
}