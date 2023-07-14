<?php
declare(strict_types=1);

namespace Retrofit\Tests\Fixtures\Converter;

use Retrofit\Converter\Converter;
use Retrofit\Converter\ConverterFactory;
use Retrofit\Internal\BuiltInConverters;
use Retrofit\Type;
use stdClass;

class TestConverterFactory implements ConverterFactory
{
    public function requestBodyConverter(Type $type): ?Converter
    {isParametrizedTypeIsScalar
        return null;
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

    public function stringConverter(): ?Converter
    {
        return null;
    }
}
