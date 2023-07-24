<?php

declare(strict_types=1);

namespace Retrofit\Core\Internal\ParameterHandler;

use Psr\Http\Message\UriInterface;
use ReflectionMethod;
use Retrofit\Core\Internal\RequestBuilder;
use Retrofit\Core\Internal\Utils\Utils;

readonly class UrlParameterHandler implements ParameterHandler
{
    public function __construct(
        private ReflectionMethod $reflectionMethod,
        private int $position,
    )
    {
    }

    public function apply(RequestBuilder $requestBuilder, mixed $value): void
    {
        if (is_null($value)) {
            throw Utils::parameterException(
                $this->reflectionMethod,
                $this->position,
                '#[Url] parameter value must not be null.',
            );
        }

        if (!is_string($value) && !$value instanceof UriInterface) {
            throw Utils::parameterException($this->reflectionMethod, $this->position, '#[Url] must be string or UriInterface type.');
        }

        $requestBuilder->setBaseUrl($value);
    }
}
