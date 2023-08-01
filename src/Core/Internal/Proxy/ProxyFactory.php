<?php

declare(strict_types=1);

namespace Retrofit\Core\Internal\Proxy;

use ReflectionClass;
use Retrofit\Core\Retrofit;

/**
 * @internal
 */
interface ProxyFactory
{
    /**
     * Creates a new proxy from given service.
     *
     * @template T of object
     * @param ReflectionClass<T> $service
     * @return T
     */
    public function create(Retrofit $retrofit, ReflectionClass $service): object;
}
