<?php
declare(strict_types=1);

namespace Retrofit;

use InvalidArgumentException;
use LogicException;
use Psr\Http\Message\UriInterface;
use ReflectionClass;
use ReflectionException;
use Retrofit\Proxy\ProxyFactory;

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
class Retrofit
{
    public function __construct(
        public readonly HttpClient $httpClient,
        public readonly UriInterface $baseUrl,
        public readonly array $converterFactories,
        private readonly ProxyFactory $proxyFactory
    )
    {
    }

    /**
     * Creates and implementation of the API endpoints defined by the {@code service} interface.
     *
     * @param string $service
     * @return object the implementation of the service
     * @throws InvalidArgumentException
     * @throws LogicException
     * @throws ReflectionException
     */
    public function create(string $service): object
    {
        $reflectionClass = new ReflectionClass($service);
        $this->validateServiceInterface($reflectionClass);
        return $this->proxyFactory->create($this, $reflectionClass);
    }

    public static function builder(): RetrofitBuilder
    {
        return new RetrofitBuilder();
    }

    private function validateServiceInterface(ReflectionClass $service): void
    {
        if (!$service->isInterface()) {
            throw new InvalidArgumentException('API declarations must be interface');
        }
    }
}
