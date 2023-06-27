<?php
declare(strict_types=1);

namespace Retrofit\Internal;

use LogicException;
use Retrofit\Converter;
use Retrofit\ConverterFactory;

class ConverterProvider
{
    /**
     * A cache of [@see ResponseBodyConverter]'s
     *
     * @var ResponseBodyConverter[]
     */
    private $responseBodyConverters = [];

    /**
     * A cache of [@see RequestBodyConverter]'s
     *
     * @var RequestBodyConverter[]
     */
    private $requestBodyConverters = [];

    /**
     * A cache of [@see StringConverter]'s
     *
     * @var StringConverter[]
     */
    private $stringConverters = [];

    /**
     * @param ConverterFactory[] $converterFactories
     */
    public function __construct(private readonly array $converterFactories)
    {
    }

//    public function getResponseBodyConverter(TypeToken $type): ResponseBodyConverter
//    {
//        $key = (string)$type;
//        if (isset($this->responseBodyConverters[$key])) {
//            return $this->responseBodyConverters[$key];
//        }
//
//        foreach ($this->converterFactories as $converterFactory) {
//            $converter = $converterFactory->responseBodyConverter($type);
//            if ($converter === null) {
//                continue;
//            }
//
//            $this->responseBodyConverters[$key] = $converter;
//
//            return $converter;
//        }
//
//        throw new LogicException(sprintf(
//            'Retrofit: Could not get response body converter for type %s',
//            $type
//        ));
//    }
//
//    public function getRequestBodyConverter(TypeToken $type): RequestBodyConverter
//    {
//        $key = (string)$type;
//        if (isset($this->requestBodyConverters[$key])) {
//            return $this->requestBodyConverters[$key];
//        }
//
//        foreach ($this->converterFactories as $converterFactory) {
//            $converter = $converterFactory->requestBodyConverter($type);
//            if ($converter === null) {
//                continue;
//            }
//
//            $this->requestBodyConverters[$key] = $converter;
//
//            return $converter;
//        }
//
//        throw new LogicException(sprintf(
//            'Retrofit: Could not get request body converter for type %s',
//            $type
//        ));
//    }

    public function getStringConverter(): Converter
    {
//        $key = (string)$type;
//        if (isset($this->stringConverters[$key])) {
//            return $this->stringConverters[$key];
//        }

        foreach ($this->converterFactories as $converterFactory) {
            $converter = $converterFactory->stringConverter();
            if (is_null($converter)) {
                continue;
            }

//            $this->stringConverters[$key] = $converter;

            return $converter;
        }

        throw new LogicException(sprintf(
            'Retrofit: Could not get string converter for type %s',
            $type
        ));
    }
}
