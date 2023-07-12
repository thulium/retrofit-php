<?php
declare(strict_types=1);

namespace Retrofit\Internal;

use Psr\Http\Message\StreamInterface;
use Retrofit\BodyConverter;
use Retrofit\Converter;
use Retrofit\ConverterFactory;
use Retrofit\Type;

readonly class BuiltInConverterFactory implements ConverterFactory
{
    public function requestBodyConverter(Type $type): ?BodyConverter
    {
        if ($type->isA(StreamInterface::class)) {
            return BuiltInConverters::StreamInterfaceRequestBodyConverter();
        }
        return BuiltInConverters::JsonEncodeRequestBodyConverter();
    }

    public function responseBodyConverter(): ?Converter
    {
        return null;
    }

    public function stringConverter(): ?Converter
    {
        return BuiltInConverters::ToStringConverter();
    }
}
