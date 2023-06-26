<?php
declare(strict_types=1);

namespace Retrofit\Internal;

use ReflectionAttribute;
use ReflectionException;
use ReflectionMethod;
use Retrofit\Attribute\HttpRequest;
use Retrofit\Attribute\Path;
use Retrofit\Call;
use Retrofit\HttpClient;
use Retrofit\Internal\Handler\ParameterHandler;
use Retrofit\Internal\Handler\PathHandler;
use Retrofit\Retrofit;

class ServiceMethodFactory
{
    /**
     * @throws ReflectionException
     */
    public static function create(Retrofit $retrofit, string $service, string $method): ServiceMethod
    {
        $reflectionMethod = new ReflectionMethod($service, $method);
        $reflectionAttributes = $reflectionMethod->getAttributes();
        $reflectionAttributeInstances = array_map(fn(ReflectionAttribute $attribute): object => $attribute->newInstance(), $reflectionAttributes);
        $reflectionAttributes1 = array_filter($reflectionAttributeInstances, fn(object $instance): bool => $instance instanceof HttpRequest);

        $pathHandlers = self::test();

        $parameterHandlers = [];
        $reflectionParameters = $reflectionMethod->getParameters();
        foreach ($reflectionParameters as $reflectionParameter) {
            $reflectionAttributes2 = $reflectionParameter->getAttributes();
            $reflectionAttribute = $reflectionAttributes2[0];
            $newInstance = $reflectionAttribute->newInstance();
            $position = $reflectionParameter->getPosition();

            /** @var ParameterHandler $var1 */
            $var1 = $pathHandlers[$reflectionAttribute->getName()];
            $var1->setAttribute($newInstance);

            $parameterHandlers[$position] = $var1;
        }
        ksort($parameterHandlers);

        /** @var HttpRequest $var */
        $var = $reflectionAttributes1[0];
        $requestBuilder = (new RequestBuilder())
            ->baseUrl($retrofit->baseUrl)
            ->httpRequest($var)
            ->parameterHandlers($parameterHandlers);

        return new class($retrofit->httpClient, $requestBuilder) implements ServiceMethod {
            public function __construct(
                private readonly HttpClient $httpClient,
                private readonly RequestBuilder $requestBuilder
            )
            {
            }

            public function invoke(array $args): Call
            {
                $request = $this->requestBuilder
                    ->withArgs($args)
                    ->build();
                return new HttpClientCall($this->httpClient, $request);
            }
        };
    }

    private static function test()
    {
        return [
            Path::class => new PathHandler(),
        ];
    }
}
