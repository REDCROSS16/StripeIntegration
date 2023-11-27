<?php

declare(strict_types=1);

namespace App\Service\Datetime;

use DateTimeImmutable;
use DateTimeZone;
use Exception;

/**
 * Class DateTimeWrapper
 */
class DateTimeWrapper
{
    /**
     * Shows default timezone
     */
    private const DEFAULT_TIMEZONE = 'UTC';

    /**
     * Returns DateTime object that contains current time
     *
     * @return DateTimeImmutable
     * @throws Exception
     */
    public static function getCurrentMoment(): DateTimeImmutable
    {
        return new DateTimeImmutable('now', new DateTimeZone(self::DEFAULT_TIMEZONE));
    }
}
