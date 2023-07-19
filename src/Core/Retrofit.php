<?php

declare(strict_types=1);

namespace Retrofit\Core;

use InvalidArgumentException;
use Psr\Http\Message\UriInterface;
use ReflectionClass;
use ReflectionException;
use Retrofit\Core\Internal\ConverterProvider;
use Retrofit\Core\Internal\Proxy\ProxyFactory;

/**
 * Retrofit adapts a PHP interface to HTTP calls by using attributes on the declared methods to define how request are
 * made. Create instances using {@link RetrofitBuilder the builder} and pass your interface to
 * {@link Retrofit::create() create} method to generate an implementation.
 *
 * For example:
 * <pre>
 * $retrofit = Retrofit::builder()
 *     ->client(...) // Implementation of the HttpClient interface
 *     ->baseUrl('https://api.example.com')
 *     ->addConverterFactory()
 *     ->build();
 *
 * $api = retrofit.create(MyApi::class);
 * $users = $api->getUsers()->execute();
 * </pre>
 */
readonly class Retrofit
{
    public function __construct(
        public HttpClient $httpClient,
        public UriInterface $baseUrl,
        public ConverterProvider $converterProvider,
        private ProxyFactory $proxyFactory,
    )
    {
    }

    /**
     * Creates and implementation of the API endpoints defined by the {@code service} interface.
     *
     * @template T of object
     * @param class-string<T> $service
     * @return T the implementation of the service
     * @throws ReflectionException
     */
    public function create(string $service): object
    {
        $reflectionClass = new ReflectionClass($service);
        $this->validateServiceInterface($reflectionClass);
        return $this->proxyFactory->create($this, $reflectionClass);
    }

    public static function Builder(): RetrofitBuilder
    {
        return new RetrofitBuilder();
    }

    /**
     * @template T of object
     * @param ReflectionClass<T> $service
     * @return void
     */
    private function validateServiceInterface(ReflectionClass $service): void
    {
        if (!$service->isInterface()) {
            throw new InvalidArgumentException("Service '{$service->getShortName()}' API declarations must be interface.");
        }
    }
}
