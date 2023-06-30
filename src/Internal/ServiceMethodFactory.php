<?php
declare(strict_types=1);

namespace Retrofit\Internal;

use ReflectionAttribute;
use ReflectionException;
use ReflectionMethod;
use Retrofit\Attribute\HttpRequest;
use Retrofit\Attribute\Url;
use Retrofit\Call;
use Retrofit\HttpClient;
use Retrofit\Internal\ParameterHandler\Factory\ParameterHandlerFactoryProvider;
use Retrofit\Internal\Utils\Utils;
use Retrofit\Retrofit;

readonly class ServiceMethodFactory
{
    /**
     * @throws ReflectionException
     */
    public static function create(Retrofit $retrofit, string $service, string $method): ServiceMethod
    {
        $reflectionMethod = new ReflectionMethod($service, $method);
        $httpRequestMethods = collect($reflectionMethod->getAttributes())
            ->map(fn(ReflectionAttribute $attribute): object => $attribute->newInstance())
            ->filter(fn(object $instance): bool => $instance instanceof HttpRequest)
            ->collect();

        if ($httpRequestMethods->isEmpty()) {
            throw Utils::methodException($reflectionMethod,
                'HTTP method annotation is required (e.g., #[GET], #[POST], etc.).');
        }

        //todo check issue https://github.com/nikic/PHP-Parser/issues/930 is fixed
        if ($httpRequestMethods->count() > 1) {
            $httpMethodNames = $httpRequestMethods->implode(fn(HttpRequest $request): string => $request::class, ', ');
            throw Utils::methodException($reflectionMethod, "Only one HTTP method is allowed. Found: [$httpMethodNames].");
        }

        $httpRequest = $httpRequestMethods->first();

        $parameterHandlers = self::getParameterHandlers($httpRequest, $retrofit, $reflectionMethod);

        $requestFactory = new RequestFactory($retrofit->baseUrl, $httpRequest, $parameterHandlers);

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

    private static function getParameterHandlers(HttpRequest $httpRequest, Retrofit $retrofit, ReflectionMethod $reflectionMethod): array
    {
        $parameterHandlerFactoryProvider = new ParameterHandlerFactoryProvider($httpRequest, $retrofit->converterProvider);

        $gotUrl = false;
        $gotPath = !is_null($httpRequest->path());

        $parameterHandlers = [];
        $reflectionParameters = $reflectionMethod->getParameters();
        foreach ($reflectionParameters as $reflectionParameter) {
            $position = $reflectionParameter->getPosition();
            $reflectionAttributes = $reflectionParameter->getAttributes();

            $reflectionAttribute = $reflectionAttributes[0];
            $newInstance = $reflectionAttribute->newInstance();

            if ($newInstance instanceof Url) {
                if ($gotUrl) {
                    throw Utils::parameterException($reflectionMethod, $position,
                        'Multiple #[Url] method attributes found.');
                }
                //todo do we really need this?
                if ($gotPath) {
                    throw Utils::parameterException($reflectionMethod, $position,
                        '#[Path] parameters may not be used with #[Url].');
                }

                $gotUrl = true;
            }

            $parameterHandlerFactory = $parameterHandlerFactoryProvider->get($reflectionAttribute->getName());
            $parameterHandler = $parameterHandlerFactory->create($newInstance, $reflectionMethod, $position);

            $parameterHandlers[$position] = $parameterHandler;
        }

        ksort($parameterHandlers);

        return $parameterHandlers;
    }
}
