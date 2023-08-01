<?php

declare(strict_types=1);

namespace Retrofit\Core\Attribute;

use Attribute;
use Retrofit\Core\Retrofit;

/**
 * URL resolved against the {@link Retrofit::$baseUrl base URL}.
 *
 * <pre>
 * #[GET]
 * public function list(#[Url] string $url): Call;
 * </pre>
 *
 * @api
 */
#[Attribute(Attribute::TARGET_PARAMETER)]
readonly class Url implements ParameterAttribute
{
}
