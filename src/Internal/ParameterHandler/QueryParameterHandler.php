<?php
declare(strict_types=1);

namespace Retrofit\Internal\ParameterHandler;

use ReflectionMethod;
use Retrofit\Converter\Converter;
use Retrofit\Internal\RequestBuilder;

readonly class QueryParameterHandler implements ParameterHandler
{
    use WithQueryParameter;

    public function __construct(
        private string $name,
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
        $requestBuilder->addQueryParam($this->name, $value, $this->encoded);
    }
}
