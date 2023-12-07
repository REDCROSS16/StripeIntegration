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

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CancelSubscribeCommand
 * @package App\Command
 */
#[AsCommand(
    name: 'app:stripe:cancel',
    description: 'Cancel subscription',
)]
class CancelSubscribeCommand extends Command
{
    // TODO: проверять время подписки, если дата меньше чем сегодня отменять подписку
    // и отправлять уведомление (добавить транспорт или мейлер)
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        return 0;
    }
}
