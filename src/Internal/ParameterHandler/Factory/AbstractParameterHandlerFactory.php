<?php
declare(strict_types=1);

namespace Retrofit\Internal\ParameterHandler\Factory;

use ReflectionMethod;
use Retrofit\Attribute\HttpRequest;
use Retrofit\Attribute\Path;
use Retrofit\Attribute\Query;
use Retrofit\Attribute\QueryName;
use Retrofit\Attribute\Url;
use Retrofit\Internal\ConverterProvider;
use Retrofit\Internal\ParameterHandler\ParameterHandler;

readonly abstract class AbstractParameterHandlerFactory
{
    public function __construct(protected ConverterProvider $converterProvider)
    {
    }

    abstract public function create(Path & Query & QueryName & Url $param, HttpRequest $httpRequest, ReflectionMethod $reflectionMethod, int $position): ParameterHandler;
}
