<?php

declare(strict_types=1);

namespace Retrofit\Core\Converter;

/**
 * Converts various values to <code>string</code> value.
 *
 * @see Converter
 * @see ConverterFactory
 *
 * @api
 */
interface StringConverter extends Converter
{
    public function convert(mixed $value): string;
}
