<?php

declare(strict_types=1);

namespace Retrofit\Core\Converter;

use Retrofit\Core\Type;

interface ConverterFactory
{
    public function requestBodyConverter(Type $type): ?Converter;

    public function responseBodyConverter(Type $type): ?Converter;

    public function stringConverter(Type $type): ?Converter;
}
