<?php

/**
 * Created by PhpStorm
 * User: red
 * Date: 26.11.2023
 * Time: 20:41
 * Project: StripeIntegration
 */

declare(strict_types=1);

namespace App\Utils\Converter;

/**
 * Class DataConverter
 * @package App\Utils\Converter
 * @author red <zvertred@gmail.com>
 */
class DataConverter
{
    /**
     * @param string|int|float|null $value
     * @return string
     */
    public static function toString(string|int|float|null $value): string
    {
        return strip_tags(str_replace(["\n", "\r"], ' ', trim((string) $value)));
    }

    /**
     * @param string|int|float|null $value
     * @return string|null
     */
    public static function toStringOrNull(string|int|float|null $value): ?string
    {
        return ($value === null || $value === '') ? null : self::toString($value);
    }

    /**
     * @param int|float|string|null $value
     * @return float
     */
    public static function toFloat(int|float|string|null $value): float
    {
        if (\is_string($value)) {
            $value = preg_replace(['~(?![\d.,-]).~', '~,~'], ['', '.'], $value);
        }

        return (float) $value;
    }

    /**
     * @param string|int|float|null $value
     * @return float|null
     */
    public static function toFloatOrNull(string|int|float|null $value): ?float
    {
        return ($value === null || $value === '') ? null : self::toFloat($value);
    }
}
