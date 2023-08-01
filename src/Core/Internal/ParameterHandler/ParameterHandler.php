<?php

declare(strict_types=1);

namespace Retrofit\Core\Internal\ParameterHandler;

use Retrofit\Core\Internal\RequestBuilder;

/**
 * @internal
 */
interface ParameterHandler
{
    public function apply(RequestBuilder $requestBuilder, mixed $value): void;
}
