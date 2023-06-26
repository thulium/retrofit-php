<?php
declare(strict_types=1);

namespace Retrofit\Internal\Handler;

use Retrofit\Attribute\Path;
use Retrofit\Internal\RequestBuilder;

interface ParameterHandler
{
    public function setAttribute(Path $instance): void;

    public function apply(RequestBuilder $requestBuilder, mixed $value): void;
}
