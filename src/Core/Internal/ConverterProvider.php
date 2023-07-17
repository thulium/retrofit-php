<?php

declare(strict_types=1);

namespace Retrofit\Core\Internal;

use Ouzo\Utilities\Arrays;
use Retrofit\Core\Converter\Converter;
use Retrofit\Core\Converter\ConverterFactory;
use Retrofit\Core\Type;
use RuntimeException;

readonly class ConverterProvider
{
    /** @param ConverterFactory[] $converterFactories */
    public function __construct(private array $converterFactories)
    {
    }

    public function getRequestBodyConverter(Type $type): Converter
    {
        /** @var ConverterFactory|null $converterFactory */
        $converterFactory = Arrays::find($this->converterFactories, fn(ConverterFactory $factory) => !is_null($factory->requestBodyConverter($type)));
        if (!is_null($converterFactory)) {
            return $converterFactory->requestBodyConverter($type);
        }
        throw new RuntimeException("Cannot find request body converter for type '{$type}'.");
    }

    public function getResponseBodyConverter(Type $type): Converter
    {
        /** @var ConverterFactory|null $converterFactory */
        $converterFactory = Arrays::find($this->converterFactories, fn(ConverterFactory $factory) => !is_null($factory->responseBodyConverter($type)));
        if (!is_null($converterFactory)) {
            return $converterFactory->responseBodyConverter($type);
        }
        throw new RuntimeException("Cannot find response body converter for type '{$type}'.");
    }

    public function getStringConverter(Type $type): Converter
    {
        /** @var ConverterFactory|null $converterFactory */
        $converterFactory = Arrays::find($this->converterFactories, fn(ConverterFactory $factory) => !is_null($factory->stringConverter($type)));
        if (!is_null($converterFactory)) {
            return $converterFactory->stringConverter($type);
        }
        throw new RuntimeException('Cannot find string converter.');
    }
}
