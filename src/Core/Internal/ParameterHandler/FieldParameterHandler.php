<?php

declare(strict_types=1);

namespace Retrofit\Core\Internal\ParameterHandler;

use Retrofit\Core\Converter\StringConverter;
use Retrofit\Core\Internal\RequestBuilder;

readonly class FieldParameterHandler implements ParameterHandler
{
    public function __construct(
        private string $name,
        private bool $encoded,
        private StringConverter $converter,
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
