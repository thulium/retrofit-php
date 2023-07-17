<?php

declare(strict_types=1);

namespace Retrofit\Tests\Fixtures\Converter;

use Retrofit\Core\Converter\Converter;
use Retrofit\Core\Converter\ConverterFactory;
use Retrofit\Core\Internal\BuiltInConverters;
use Retrofit\Core\Type;
use stdClass;

class TestConverterFactory implements ConverterFactory
{
    public function requestBodyConverter(Type $type): ?Converter
    {
        return BuiltInConverters::JsonEncodeRequestBodyConverter();
    }

    public function responseBodyConverter(Type $type): ?Converter
    {
        if ($type->isA(stdClass::class)) {
            return BuiltInConverters::StdClassResponseBodyConverter();
        }
        if ($type->isA('array')) {
            return BuiltInConverters::ArrayResponseBodyConverter($type);
        }
        if ($type->isScalar()) {
            return BuiltInConverters::ScalarTypeResponseBodyConverter($type);
        }
        return null;
    }

    public function stringConverter(Type $type): ?Converter
    {
        if (!$type->isScalar()) {
            return BuiltInConverters::ToStringConverter();
        }
        return null;
    }
}
