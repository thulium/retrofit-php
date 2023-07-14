<?php
declare(strict_types=1);

namespace Retrofit\Internal;

use Psr\Http\Message\StreamInterface;
use Retrofit\Converter\Converter;
use Retrofit\Converter\ConverterFactory;
use Retrofit\Converter\RequestBodyConverter;
use Retrofit\Type;

readonly class BuiltInConverterFactory implements ConverterFactory
{
    public function requestBodyConverter(Type $type): ?RequestBodyConverter
    {
        if ($type->isA(StreamInterface::class)) {
            return BuiltInConverters::StreamInterfaceRequestBodyConverter();
        }
        return BuiltInConverters::JsonEncodeRequestBodyConverter();
    }

    public function responseBodyConverter(Type $type): ?Converter
    {
        if ($type->isA('void')) {
            return BuiltInConverters::VoidResponseBodyConverter();
        }
        if ($type->isA(StreamInterface::class)) {
            return BuiltInConverters::StreamInterfaceResponseBodyConverter();
        }
        return null;
    }

    public function stringConverter(): ?Converter
    {
        return BuiltInConverters::ToStringConverter();
    }
}
