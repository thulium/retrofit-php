<?php

declare(strict_types=1);

namespace Retrofit\Core\Internal\ParameterHandler;

use ReflectionMethod;
use Retrofit\Core\Converter\Converter;
use Retrofit\Core\Internal\RequestBuilder;
use Retrofit\Core\Internal\Utils\Utils;

readonly class PathParameterHandler implements ParameterHandler
{
    public function __construct(
        private string $name,
        private bool $encoded,
        private Converter $converter,
        private ReflectionMethod $reflectionMethod,
        private int $position,
    ) {
    }

    public function apply(RequestBuilder $requestBuilder, mixed $value): void
    {
        if (is_null($value)) {
            throw Utils::parameterException(
                $this->reflectionMethod,
                $this->position,
                "#[Path] parameter '{$this->name}' value must not be null.",
            );
        }

        $value = $this->converter->convert($value);
        $requestBuilder->addPathParam($this->name, $value, $this->encoded);
    }
}
