<?php

declare(strict_types=1);

namespace Retrofit\Core\Converter;

use Retrofit\Core\Retrofit;

/**
 * Convert objects to and from their representation in HTTP. Instances are created by {@link ConverterFactory} installed
 * into the {@link Retrofit} instance.
 *
 * @see ConverterFactory
 * @see RequestBodyConverter
 * @see ResponseBodyConverter
 * @see StringConverter
 *
 * @api
 */
interface Converter
{
}
