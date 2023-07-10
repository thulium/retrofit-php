<?php
declare(strict_types=1);

namespace Retrofit\Internal;

use Retrofit\Converter;
use Retrofit\ConverterFactory;

readonly class ConverterProvider
{
    /** @param ConverterFactory[] $converterFactories */
    public function __construct(private array $converterFactories)
    {
    }

    public function getRequestBodyConverter(): Converter
    {
        foreach ($this->converterFactories as $converterFactory) {
            $converter = $converterFactory->requestBodyConverter();
            if (is_null($converter)) {
                continue;
            }
            return $converter;
        }

        return BuiltInConverters::JsonEncodeRequestBodyConverter();
    }

    public function getStringConverter(): Converter
    {
        return BuiltInConverters::ToStringConverter();
    }
}
