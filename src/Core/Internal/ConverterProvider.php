<?php

declare(strict_types=1);

namespace Retrofit\Core\Internal;

use Ouzo\Utilities\Arrays;
use Retrofit\Core\Converter\ConverterFactory;
use Retrofit\Core\Converter\RequestBodyConverter;
use Retrofit\Core\Converter\ResponseBodyConverter;
use Retrofit\Core\Converter\StringConverter;
use Retrofit\Core\Type;
use RuntimeException;

readonly class ConverterProvider
{
    /** @param ConverterFactory[] $converterFactories */
    public function __construct(private array $converterFactories)
    {
    }

    public function getRequestBodyConverter(Type $type): RequestBodyConverter
    {
        /** @var ConverterFactory|null $converterFactory */
        $converterFactory = Arrays::find($this->converterFactories, fn(ConverterFactory $factory) => !is_null($factory->requestBodyConverter($type)));
        if (!is_null($converterFactory)) {
            $converter = $converterFactory->requestBodyConverter($type);
            if (!is_null($converter)) {
                return $converter;
            }
        }
        throw new RuntimeException("Cannot find request body converter for type '{$type}'.");
    }

    public function getResponseBodyConverter(Type $type): ResponseBodyConverter
    {
        /** @var ConverterFactory|null $converterFactory */
        $converterFactory = Arrays::find($this->converterFactories, fn(ConverterFactory $factory) => !is_null($factory->responseBodyConverter($type)));
        if (!is_null($converterFactory)) {
            $converter = $converterFactory->responseBodyConverter($type);
            if (!is_null($converter)) {
                return $converter;
            }
        }
        throw new RuntimeException("Cannot find response body converter for type '{$type}'.");
    }

    public function getStringConverter(Type $type): StringConverter
    {
        /** @var ConverterFactory|null $converterFactory */
        $converterFactory = Arrays::find($this->converterFactories, fn(ConverterFactory $factory) => !is_null($factory->stringConverter($type)));
        if (!is_null($converterFactory)) {
            $converter = $converterFactory->stringConverter($type);
            if (!is_null($converter)) {
                return $converter;
            }
        }
        throw new RuntimeException('Cannot find string converter.');
    }
}
