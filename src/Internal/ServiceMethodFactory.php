<?php
declare(strict_types=1);

namespace Retrofit\Internal;

use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\FluentArray;
use Ouzo\Utilities\Joiner;
use Ouzo\Utilities\Strings;
use phpDocumentor\Reflection\DocBlockFactory;
use ReflectionAttribute;
use ReflectionMethod;
use Retrofit\Attribute\Field;
use Retrofit\Attribute\FieldMap;
use Retrofit\Attribute\FormUrlEncoded;
use Retrofit\Attribute\Headers;
use Retrofit\Attribute\HttpRequest;
use Retrofit\Attribute\Multipart;
use Retrofit\Attribute\Part;
use Retrofit\Attribute\PartMap;
use Retrofit\Attribute\Response\ErrorBody;
use Retrofit\Attribute\Response\ResponseBody;
use Retrofit\Attribute\Url;
use Retrofit\Call;
use Retrofit\Converter\Converter;
use Retrofit\Converter\ResponseBodyConverter;
use Retrofit\HttpClient;
use Retrofit\Internal\ParameterHandler\Factory\ParameterHandlerFactoryProvider;
use Retrofit\Internal\Utils\Utils;
use Retrofit\Retrofit;
use Retrofit\Type;

readonly class ServiceMethodFactory
{
    public function __construct(
        private Retrofit $retrofit,
        private ParameterHandlerFactoryProvider $parameterHandlerFactoryProvider
    )
    {
    }

    public function create(string $service, string $method): ServiceMethod
    {
        $reflectionMethod = new ReflectionMethod($service, $method);

        $httpRequest = $this->getHttpRequest($reflectionMethod);
        $encoding = $this->getEncoding($httpRequest, $reflectionMethod);
        $defaultHeaders = $this->getDefaultHeaders($reflectionMethod);
        $parameterHandlers = $this->getParameterHandlers($httpRequest, $encoding, $reflectionMethod);
        $responseBodyConverter = $this->getResponseBodyConverter($reflectionMethod);
        $errorBodyConverter = $this->getErrorBodyConverter($reflectionMethod);

        $requestFactory = new RequestFactory($this->retrofit->baseUrl, $httpRequest, $defaultHeaders, $parameterHandlers);

        return new class($this->retrofit->httpClient, $requestFactory, $responseBodyConverter, $errorBodyConverter) implements ServiceMethod {
            public function __construct(
                private readonly HttpClient $httpClient,
                private readonly RequestFactory $requestFactory,
                private readonly ResponseBodyConverter $responseBodyConverter,
                private readonly ?ResponseBodyConverter $errorBodyConverter
            )
            {
            }

            public function invoke(array $args): Call
            {
                $request = $this->requestFactory->create($args);
                return new HttpClientCall($this->httpClient, $request, $this->responseBodyConverter, $this->errorBodyConverter);
            }
        };
    }

    private function getHttpRequest(ReflectionMethod $reflectionMethod): HttpRequest
    {
        $httpRequestMethods = FluentArray::from($reflectionMethod->getAttributes())
            ->map(fn(ReflectionAttribute $attribute): object => $attribute->newInstance())
            ->filter(fn(object $instance): bool => $instance instanceof HttpRequest)
            ->toArray();

        if (empty($httpRequestMethods)) {
            throw Utils::methodException($reflectionMethod,
                'HTTP method annotation is required (e.g., #[GET], #[POST], etc.).');
        }

        //todo check issue https://github.com/nikic/PHP-Parser/issues/930 is fixed
        if (count($httpRequestMethods) > 1) {
            $httpMethodNames = Joiner::on(', ')
                ->mapValues(fn(HttpRequest $request): string => $request::class)
                ->join($httpRequestMethods);
            throw Utils::methodException($reflectionMethod, "Only one HTTP method is allowed. Found: [$httpMethodNames].");
        }

        return Arrays::first($httpRequestMethods);
    }

    private function getEncoding(HttpRequest $httpRequest, ReflectionMethod $reflectionMethod): ?Encoding
    {
        $encodingAttributes = FluentArray::from($reflectionMethod->getAttributes())
            ->map(fn(ReflectionAttribute $attribute): object => $attribute->newInstance())
            ->filter(fn(object $instance): bool => $instance instanceof FormUrlEncoded || $instance instanceof Multipart)
            ->toArray();

        if (empty($encodingAttributes)) {
            return null;
        }

        if (count($encodingAttributes) > 1) {
            throw Utils::methodException($reflectionMethod, 'Only one encoding annotation is allowed.');
        }

        /** @var FormUrlEncoded|Multipart $encoding */
        $encoding = Arrays::first($encodingAttributes);
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
        $headers = FluentArray::from($reflectionMethod->getAttributes())
            ->map(fn(ReflectionAttribute $attribute): object => $attribute->newInstance())
            ->filter(fn(object $instance): bool => $instance instanceof Headers)
            ->firstOr(null);
        if (!is_null($headers)) {
            $converter = $this->retrofit->converterProvider->getStringConverter(new Type('string'));
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
        $docCommentParams = [];
        $docComment = $reflectionMethod->getDocComment();
        if ($docComment !== false) {
            $docBlockFactory = DocBlockFactory::createInstance();
            $docBlock = $docBlockFactory->create($docComment);
            $docCommentParams = $docBlock->getTagsByName('param');
        }

        $gotUrl = false;
        $gotField = false;
        $gotPart = false;

        $parameterHandlers = [];
        $reflectionParameters = Utils::sortParameterAttributesByPriorities($reflectionMethod->getParameters());
        foreach ($reflectionParameters as $reflectionParameter) {
            $position = $reflectionParameter->getPosition();
            $reflectionAttributes = $reflectionParameter->getAttributes();

            if (!$reflectionParameter->hasType()) {
                throw Utils::parameterException($reflectionMethod, $position, 'Type is required.');
            }

            if (empty($reflectionAttributes)) {
                throw Utils::parameterException($reflectionMethod, $position, 'No Retrofit attribute found.');
            }

            $reflectionAttribute = $reflectionAttributes[0];
            $newInstance = $reflectionAttribute->newInstance();

            if ($newInstance instanceof Url) {
                if ($gotUrl) {
                    throw Utils::parameterException($reflectionMethod, $position,
                        'Multiple #[Url] method attributes found.');
                }

                $gotUrl = true;
            } elseif ($newInstance instanceof Field || $newInstance instanceof FieldMap) {
                $gotField = true;
            } elseif ($newInstance instanceof Part || $newInstance instanceof PartMap) {
                $gotPart = true;
            }

            $type = Type::create($reflectionMethod, $reflectionParameter, $docCommentParams);

            $parameterHandlerFactory = $this->parameterHandlerFactoryProvider->get($reflectionAttribute->getName());
            $parameterHandler = $parameterHandlerFactory->create($newInstance, $httpRequest, $encoding, $reflectionMethod, $position, $type);

            $parameterHandlers[$position] = $parameterHandler;
        }

        if ($encoding === Encoding::FORM_URL_ENCODED && !$gotField) {
            throw Utils::methodException($reflectionMethod,
                '#[FormUrlEncoded] method must contain at least one #[Field] or #[FieldMap].');
        }

        if ($encoding === Encoding::MULTIPART && !$gotPart) {
            throw Utils::methodException($reflectionMethod,
                '#[Multipart] method must contain at least one #[Part] or #[PartMap].');
        }

        ksort($parameterHandlers);

        return $parameterHandlers;
    }

    private function getResponseBodyConverter(ReflectionMethod $reflectionMethod): Converter
    {
        $reflectionAttributes = $reflectionMethod->getAttributes(ResponseBody::class);
        if (empty($reflectionAttributes)) {
            throw Utils::methodException($reflectionMethod, '#[ResponseBody] attribute is required.');
        }
        return $this->getBodyConverter($reflectionAttributes[0]);
    }

    private function getErrorBodyConverter(ReflectionMethod $reflectionMethod): ?Converter
    {
        $reflectionAttributes = $reflectionMethod->getAttributes(ErrorBody::class);
        if (empty($reflectionAttributes)) {
            return null;
        }
        return $this->getBodyConverter($reflectionAttributes[0]);
    }

    private function getBodyConverter(ReflectionAttribute $reflectionAttribute): Converter
    {
        /** @var ResponseBody|ErrorBody $body */
        $body = $reflectionAttribute->newInstance();
        $responseType = new Type($body->rawType(), $body->parametrizedType());
        return $this->retrofit->converterProvider->getResponseBodyConverter($responseType);
    }
}
