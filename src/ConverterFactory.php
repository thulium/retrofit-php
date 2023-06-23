<?php
declare(strict_types=1);

namespace Retrofit;

interface ConverterFactory
{
    public function requestBodyConverter(): ?Converter;

    public function responseBodyConverter(): ?Converter;
}
