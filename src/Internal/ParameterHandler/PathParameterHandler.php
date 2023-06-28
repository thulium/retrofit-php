<?php
declare(strict_types=1);

namespace Retrofit\Internal\ParameterHandler;

use ReflectionMethod;
use Retrofit\Converter;
use Retrofit\Internal\RequestBuilder;
use Retrofit\Internal\Utils\Utils;

readonly class PathParameterHandler implements ParameterHandler
{
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
            throw Utils::parameterException($this->reflectionMethod, $this->position, "Path parameter '{$this->name}' value must not be null.");
        }

        $requestBuilder->addPathParam($this->name, $this->converter->convert($value), $this->encoded);
    }
}
