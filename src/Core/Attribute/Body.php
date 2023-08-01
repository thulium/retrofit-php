<?php

declare(strict_types=1);

namespace Retrofit\Core\Attribute;

use Attribute;

/**
 * Use this annotation on a service method param when you want to directly control the request body of a POST/PUT
 * request. The object will be serialized using the {@link Retrofit} instance {@link Converter} and the result will be
 * set directly as the request body.
 *
 * Body parameters may not be <code>null</code>.
 *
 * @api
 */
#[Attribute(Attribute::TARGET_PARAMETER)]
readonly class Body implements ParameterAttribute
{
}
