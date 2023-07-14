<?php
declare(strict_types=1);

namespace Retrofit\Converter;

use Retrofit\Type;

interface ConverterFactory
{
    public function requestBodyConverter(Type $type): ?Converter;

    public function responseBodyConverter(Type $type): ?Converter;

    public function stringConverter(Type $type): ?Converter;
}
