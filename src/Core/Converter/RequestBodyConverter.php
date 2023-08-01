<?php

declare(strict_types=1);

namespace Retrofit\Core\Converter;

use Psr\Http\Message\StreamInterface;

/**
 * Converts various values to {@link StreamInterface} to be used as a request body.
 *
 * @see Converter
 * @see ConverterFactory
 *
 * @api
 */
interface RequestBodyConverter extends Converter
{
    public function convert(mixed $value): StreamInterface;
}
