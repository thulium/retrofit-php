<?php
declare(strict_types=1);

namespace Retrofit\Internal;

use Retrofit\Converter\Converter;
use Retrofit\Converter\ConverterFactory;
use Retrofit\Type;
use RuntimeException;

readonly class ConverterProvider
{
    /** @param ConverterFactory[] $converterFactories */
    public function __construct(private array $converterFactories)
    {
    }

    public function getRequestBodyConverter(Type $type): Converter
    {
        foreach ($this->converterFactories as $converterFactory) {
            $converter = $converterFactory->requestBodyConverter($type);
            if (is_null($converter)) {
                continue;
            }
            return $converter;
        }

        //todo
        throw new RuntimeException('getRequestBodyConverter');
    }

    public function getResponseBodyConverter(Type $type): Converter
    {
        foreach ($this->converterFactories as $converterFactory) {
            $converter = $converterFactory->responseBodyConverter($type);
            if (is_null($converter)) {
                continue;
            }
            return $converter;
        }

        //todo
        throw new RuntimeException('getResponseBodyConverter');
    }

    public function getStringConverter(): Converter
    {
        return BuiltInConverters::ToStringConverter();
    }
}
