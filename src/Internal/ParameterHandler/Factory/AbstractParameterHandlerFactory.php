<?php
declare(strict_types=1);

namespace Retrofit\Internal\ParameterHandler\Factory;

use ReflectionMethod;
use Retrofit\Attribute\HttpRequest;
use Retrofit\Attribute\Path;
use Retrofit\Internal\ConverterProvider;
use Retrofit\Internal\ParameterHandler\ParameterHandler;

readonly abstract class AbstractParameterHandlerFactory
{
    public function __construct(
        protected HttpRequest $httpRequest,
        protected ConverterProvider $converterProvider
    )
    {
    }

    abstract public function create(Path $path, ReflectionMethod $reflectionMethod, int $position): ParameterHandler;
}
