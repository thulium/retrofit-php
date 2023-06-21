<?php
declare(strict_types=1);

namespace Retrofit;

class ConverterFactory
{
    public function requestBodyConverter(): ?Converter
    {
        return null;
    }

    public function responseBodyConverter(): ?Converter
    {
        return null;
    }
}
