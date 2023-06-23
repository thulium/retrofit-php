<?php
declare(strict_types=1);

namespace Retrofit\Proxy;

use ReflectionClass;
use Retrofit\Retrofit;

interface ProxyFactory
{
    public function create(Retrofit $retrofit, ReflectionClass $service): ?object;
}
