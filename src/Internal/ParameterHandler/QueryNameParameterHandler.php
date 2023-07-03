<?php
declare(strict_types=1);

namespace Retrofit\Internal\ParameterHandler;

use ReflectionMethod;
use Retrofit\Converter;
use Retrofit\Internal\RequestBuilder;

readonly class QueryNameParameterHandler implements ParameterHandler
{
    use WithQueryParameter;

    public function __construct(
        private bool $encoded,
        private Converter $converter,
        private ReflectionMethod $reflectionMethod,
        private int $position
    )
    {
    }

    public function apply(RequestBuilder $requestBuilder, mixed $value): void
    {
        if (is_null($value)) {
            return;
        }

        $value = $this->validateAndConvert($value);
        $requestBuilder->addQueryParam($value, null, $this->encoded);
    }
}
