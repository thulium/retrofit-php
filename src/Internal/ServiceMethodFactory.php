<?php
declare(strict_types=1);

namespace Retrofit\Internal;

use Ouzo\Utilities\Strings;
use ReflectionAttribute;
use ReflectionException;
use ReflectionMethod;
use Retrofit\Attribute\FormUrlEncoded;
use Retrofit\Attribute\Headers;
use Retrofit\Attribute\HttpRequest;
use Retrofit\Attribute\Multipart;
use Retrofit\Attribute\Url;
use Retrofit\Call;
use Retrofit\HttpClient;
use Retrofit\Internal\ParameterHandler\Factory\ParameterHandlerFactoryProvider;
use Retrofit\Internal\Utils\Utils;
use Retrofit\Retrofit;

readonly class ServiceMethodFactory
{
    public function __construct(
        private Retrofit $retrofit,
        private ParameterHandlerFactoryProvider $parameterHandlerFactoryProvider
    )
    {
    }

    /**
     * @throws ReflectionException
     */
    public function create(string $service, string $method): ServiceMethod
    {
        $reflectionMethod = new ReflectionMethod($service, $method);

        $httpRequest = $this->getHttpRequest($reflectionMethod);
        $encoding = $this->getEncoding($httpRequest, $reflectionMethod);
        $defaultHeaders = $this->getDefaultHeaders($reflectionMethod);
        $parameterHandlers = $this->getParameterHandlers($httpRequest, $encoding, $reflectionMethod);

        $requestFactory = new RequestFactory($this->retrofit->baseUrl, $httpRequest, $defaultHeaders, $parameterHandlers);

        return new class($this->retrofit->httpClient, $requestFactory) implements ServiceMethod {
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

    private function getHttpRequest(ReflectionMethod $reflectionMethod): HttpRequest
    {
        $httpRequestMethods = collect($reflectionMethod->getAttributes())
            ->map(fn(ReflectionAttribute $attribute): object => $attribute->newInstance())
            ->filter(fn(object $instance): bool => $instance instanceof HttpRequest);

        if ($httpRequestMethods->isEmpty()) {
            throw Utils::methodException($reflectionMethod,
                'HTTP method annotation is required (e.g., #[GET], #[POST], etc.).');
        }

        //todo check issue https://github.com/nikic/PHP-Parser/issues/930 is fixed
        if ($httpRequestMethods->count() > 1) {
            $httpMethodNames = $httpRequestMethods->implode(fn(HttpRequest $request): string => $request::class, ', ');
            throw Utils::methodException($reflectionMethod, "Only one HTTP method is allowed. Found: [$httpMethodNames].");
        }

        return $httpRequestMethods->first();
    }

    private function getEncoding(HttpRequest $httpRequest, ReflectionMethod $reflectionMethod): ?Encoding
    {
        $encodingAttributes = collect($reflectionMethod->getAttributes())
            ->map(fn(ReflectionAttribute $attribute): object => $attribute->newInstance())
            ->filter(fn(object $instance): bool => $instance instanceof FormUrlEncoded || $instance instanceof Multipart);

        if ($encodingAttributes->isEmpty()) {
            return null;
        }

        if ($encodingAttributes->count() > 1) {
            throw Utils::methodException($reflectionMethod, 'Only one encoding annotation is allowed.');
        }

        /** @var FormUrlEncoded|Multipart $encoding */
        $encoding = $encodingAttributes->first();
        if ($encoding instanceof FormUrlEncoded) {
            if (!$httpRequest->hasBody()) {
                throw Utils::methodException($reflectionMethod,
                    '#[FormUrlEncoded] can only be specified on HTTP methods with request body (e.g., #[POST]).');
            }
            return Encoding::FORM_URL_ENCODED;
        } else {
            if (!$httpRequest->hasBody()) {
                throw Utils::methodException($reflectionMethod,
                    '#[Multipart] can only be specified on HTTP methods with request body (e.g., #[POST]).');
            }
            return Encoding::MULTIPART;
        }
    }

    private function getDefaultHeaders(ReflectionMethod $reflectionMethod): array
    {
        $defaultHeaders = [];
        /** @var Headers|null $headers */
        $headers = collect($reflectionMethod->getAttributes())
            ->map(fn(ReflectionAttribute $attribute): object => $attribute->newInstance())
            ->filter(fn(object $instance): bool => $instance instanceof Headers)
            ->first();
        if (!is_null($headers)) {
            $converter = $this->retrofit->converterProvider->getStringConverter();
            $value = $headers->value();
            foreach ($value as $entryKey => $entryValue) {
                if (Strings::isBlank($entryKey)) {
                    throw Utils::methodException($reflectionMethod, 'Headers map contained empty key.');
                }
                if (is_null($entryValue)) {
                    throw Utils::methodException($reflectionMethod,
                        "Headers map contained null value for key '{$entryKey}'.");
                }

                $entryValue = $converter->convert($entryValue);
                $defaultHeaders[$entryKey] = $entryValue;
            }
        }
        return $defaultHeaders;
    }

    private function getParameterHandlers(HttpRequest $httpRequest, ?Encoding $encoding, ReflectionMethod $reflectionMethod): array
    {
        $gotUrl = false;

        $parameterHandlers = [];
        $reflectionParameters = Utils::sortParameterAttributesByPriorities($reflectionMethod->getParameters());
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

                $gotUrl = true;
            }

            $parameterHandlerFactory = $this->parameterHandlerFactoryProvider->get($reflectionAttribute->getName());
            $parameterHandler = $parameterHandlerFactory->create($newInstance, $httpRequest, $encoding, $reflectionMethod, $position);

            $parameterHandlers[$position] = $parameterHandler;
        }

        ksort($parameterHandlers);

        return $parameterHandlers;
    }
}
