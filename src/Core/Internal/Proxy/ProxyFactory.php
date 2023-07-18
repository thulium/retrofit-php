<?php

declare(strict_types=1);

namespace Retrofit\Core\Internal\Proxy;

use ReflectionClass;
use Retrofit\Core\Retrofit;

/**
 * @template T of object
 */
interface ProxyFactory
{
    /**
     * Creates a new proxy from given service.
     *
     * @param Retrofit $retrofit
     * @param ReflectionClass<T> $service
     * @return object
     */
    public function create(Retrofit $retrofit, ReflectionClass $service): object;
}
