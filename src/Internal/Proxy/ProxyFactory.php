<?php
declare(strict_types=1);

namespace Retrofit\Internal\Proxy;

use ReflectionClass;
use Retrofit\Retrofit;

interface ProxyFactory
{
    /**
     * Creates a new proxy from given service.
     */
    public function create(Retrofit $retrofit, ReflectionClass $service): object;
}
