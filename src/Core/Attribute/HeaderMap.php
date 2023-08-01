<?php

declare(strict_types=1);

namespace Retrofit\Core\Attribute;

use Attribute;

/**
 * Adds headers specified in the map.
 *
 * Values in the map are converted to strings using {@link ConverterFactory::stringConverter()}.
 *
 * Simple Example:
 * <pre>
 * #[GET('/search')]
 * public function list(#[HeaderMap] array $headers);
 * </pre>
 *
 * Calling with <code>$foo->list(['Accept' => 'text/plain', 'Accept-Charset' => 'utf-8'])</code> yields /search with
 * headers <code>Accept: text/plain</code> and <code>Accept-Charset: utf-8</code>.
 *
 * @see Header
 * @see Headers
 *
 * @api
 */
#[Attribute(Attribute::TARGET_PARAMETER)]
readonly class HeaderMap implements ParameterAttribute
{
}
