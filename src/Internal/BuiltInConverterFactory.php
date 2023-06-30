<?php
declare(strict_types=1);

namespace Retrofit\Internal;

use Retrofit\Converter;
use Retrofit\ConverterFactory;

readonly class BuiltInConverterFactory implements ConverterFactory
{
    public function requestBodyConverter(): ?Converter
    {
        return null;
    }

    public function responseBodyConverter(): ?Converter
    {
        return null;
    }

    public function stringConverter(): ?Converter
    {
        return BuiltInConverters::toStringConverter();
    }
}
