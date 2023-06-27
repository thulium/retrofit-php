<?php
declare(strict_types=1);

namespace Retrofit\Internal\ParameterHandler;

use InvalidArgumentException;
use Retrofit\Converter;
use Retrofit\Internal\RequestBuilder;

readonly class PathParameterHandler implements ParameterHandler
{
    public function __construct(
        private string $name,
        private bool $encoded,
        private Converter $converter
    )
    {
    }

    public function apply(RequestBuilder $requestBuilder, mixed $value): void
    {
        if (is_null($value)) {
            throw new InvalidArgumentException("Path parameter '{$this->name}' value must not be null");
        }

        $requestBuilder->addPathParam($this->name, $this->converter->convert($value), $this->encoded);
    }
}
