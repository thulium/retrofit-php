<?php

declare(strict_types=1);

namespace Retrofit\Tests\Fixtures\Converter;

use Retrofit\Core\Converter\ConverterFactory;
use Retrofit\Core\Converter\RequestBodyConverter;
use Retrofit\Core\Converter\ResponseBodyConverter;
use Retrofit\Core\Converter\StringConverter;
use Retrofit\Core\Internal\BuiltInConverters;
use Retrofit\Core\Type;
use stdClass;

class TestConverterFactory implements ConverterFactory
{
    public function requestBodyConverter(Type $type): ?RequestBodyConverter
    {
        return BuiltInConverters::JsonEncodeRequestBodyConverter();
    }

    public function responseBodyConverter(Type $type): ?ResponseBodyConverter
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

    public function stringConverter(Type $type): ?StringConverter
    {
        if (!$type->isScalar()) {
            return BuiltInConverters::ToStringConverter();
        }
        return null;
    }
}
