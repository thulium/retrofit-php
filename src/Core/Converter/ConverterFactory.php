<?php

declare(strict_types=1);

namespace Retrofit\Core\Converter;

use Retrofit\Core\Type;

interface ConverterFactory
{
    public function requestBodyConverter(Type $type): ?RequestBodyConverter;

    public function responseBodyConverter(Type $type): ?ResponseBodyConverter;

    public function stringConverter(Type $type): ?StringConverter;
}
