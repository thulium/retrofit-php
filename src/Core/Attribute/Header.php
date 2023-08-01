<?php

declare(strict_types=1);

namespace Retrofit\Core\Attribute;

use Attribute;

/**
 * Replaces the header with the value of its target.
 *
 * <pre>
 * #[GET('/')]
 * public function foo(#[Header('Accept-Language')] String $lang): Call;
 * </pre>
 *
 * Header parameters may be null which will omit them from the request. Passing an array will result in a header for
 * each non-null item.
 *
 * @see HeaderMap
 * @see Headers
 *
 * @api
 */
#[Attribute(Attribute::TARGET_PARAMETER)]
readonly class Header implements ParameterAttribute
{
    public function __construct(private string $name)
    {
    }

    public function name(): string
    {
        return $this->name;
    }
}
