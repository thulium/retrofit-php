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
use Retrofit\Internal\ParameterHandler\Factory\PathAbstractParameterHandlerFactory;
use Retrofit\Retrofit;

readonly class ServiceMethodFactory
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

        $converterProvider = new ConverterProvider($retrofit->converterFactories);
        $pathParameterHandlerFactories = self::parameterHandlerFactories($converterProvider);

        $parameterHandlers = [];
        $reflectionParameters = $reflectionMethod->getParameters();
        foreach ($reflectionParameters as $reflectionParameter) {
            $reflectionAttributes2 = $reflectionParameter->getAttributes();
            $reflectionAttribute = $reflectionAttributes2[0];
            $newInstance = $reflectionAttribute->newInstance();

            $parameterHandlerFactory = $pathParameterHandlerFactories[$reflectionAttribute->getName()];
            $parameterHandler = $parameterHandlerFactory->create($reflectionParameter->getName(), $newInstance);

            $parameterHandlers[$reflectionParameter->getPosition()] = $parameterHandler;
        }
        ksort($parameterHandlers);

        /** @var HttpRequest $var */
        $var = $reflectionAttributes1[0];

        $requestFactory = new RequestFactory($retrofit->baseUrl, $var, $parameterHandlers);

        return new class($retrofit->httpClient, $requestFactory) implements ServiceMethod {
            public function __construct(
                private readonly HttpClient $httpClient,
                private readonly RequestFactory $requestFactory
            )
            {
            }

            public function invoke(array $args): Call
            {
                $request = $this->requestFactory->create($args);
                return new HttpClientCall($this->httpClient, $request);
            }
        };
    }

    private static function parameterHandlerFactories(ConverterProvider $converterProvider): array
    {
        return [
            Path::class => new PathAbstractParameterHandlerFactory($converterProvider),
        ];
    }
}
