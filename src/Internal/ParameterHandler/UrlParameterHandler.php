<?php
declare(strict_types=1);

namespace Retrofit\Internal\ParameterHandler;

use ReflectionMethod;
use Retrofit\Internal\RequestBuilder;
use Retrofit\Internal\Utils\Utils;

readonly class UrlParameterHandler implements ParameterHandler
{
    public function __construct(
        private ReflectionMethod $reflectionMethod,
        private int $position
    )
    {
    }

    public function apply(RequestBuilder $requestBuilder, mixed $value): void
    {
        if (is_null($value)) {
            throw Utils::parameterException($this->reflectionMethod, $this->position,
                "#[Url] parameter value must not be null.");
        }

        $requestBuilder->setBaseUrl($value);
    }
}
