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

        throw new RuntimeException("Cannot find request body converter for type '{$type}'.");
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

        throw new RuntimeException("Cannot find response body converter for type '{$type}'.");
    }

    public function getStringConverter(Type $type): Converter
    {
        foreach ($this->converterFactories as $converterFactory) {
            $converter = $converterFactory->stringConverter($type);
            if (is_null($converter)) {
                continue;
            }
            return $converter;
        }

        throw new RuntimeException('Cannot find string converter.');
    }
}
