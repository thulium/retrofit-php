<?php
declare(strict_types=1);

namespace Retrofit\Internal\ParameterHandler;

use Retrofit\Converter;
use Retrofit\Internal\RequestBuilder;

readonly class FieldParameterHandler implements ParameterHandler
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
            return;
        }

        $value = $this->converter->convert($value);
        $requestBuilder->addFormField($this->name, $value, $this->encoded);
    }
}
