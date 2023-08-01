<?php

declare(strict_types=1);

namespace Retrofit\Core\Converter;

use Psr\Http\Message\StreamInterface;

/**
 * Converts {@link StreamInterface} value to various values be used as a response body.
 *
 * @see Converter
 * @see ConverterFactory
 *
 * @api
 */
interface ResponseBodyConverter extends Converter
{
    public function convert(StreamInterface $value): mixed;
}
