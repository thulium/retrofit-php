<?php
declare(strict_types=1);

namespace Retrofit;

interface ConverterFactory
{
    public function requestBodyConverter(Type $type): ?Converter;

    public function responseBodyConverter(): ?Converter;

    public function stringConverter(): ?Converter;
}
