<?php
declare(strict_types=1);

namespace Retrofit\Core\Internal\ParameterHandler;

use Retrofit\Core\Converter\Converter;
use Retrofit\Core\Internal\RequestBuilder;

readonly class HeaderParameterHandler implements ParameterHandler
{
    public function __construct(
        private string $name,
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
        $requestBuilder->addHeader($this->name, $value);
    }
}
