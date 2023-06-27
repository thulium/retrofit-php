<?php
declare(strict_types=1);

namespace Retrofit\Internal\ParameterHandler;

use Retrofit\Internal\RequestBuilder;

interface ParameterHandler
{
    public function apply(RequestBuilder $requestBuilder, mixed $value): void;
}
