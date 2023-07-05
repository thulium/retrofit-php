<?php
declare(strict_types=1);

namespace Retrofit\Internal\ParameterHandler\Factory;

use ReflectionMethod;
use Retrofit\Attribute\Header;
use Retrofit\Attribute\HeaderMap;
use Retrofit\Attribute\HttpRequest;
use Retrofit\Attribute\Path;
use Retrofit\Attribute\Query;
use Retrofit\Attribute\QueryMap;
use Retrofit\Attribute\QueryName;
use Retrofit\Attribute\Url;
use Retrofit\Internal\ConverterProvider;
use Retrofit\Internal\Encoding;
use Retrofit\Internal\ParameterHandler\ParameterHandler;

readonly abstract class AbstractParameterHandlerFactory
{
    public function __construct(protected ConverterProvider $converterProvider)
    {
    }

    abstract public function create(
        Header & HeaderMap & Path & Query & QueryMap & QueryName & Url $param,
        HttpRequest $httpRequest,
        ?Encoding $encoding,
        ReflectionMethod $reflectionMethod,
        int $position
    ): ParameterHandler;
}
