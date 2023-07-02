<?php
declare(strict_types=1);

namespace Retrofit\Internal\ParameterHandler\Factory;

use ReflectionMethod;
use Retrofit\Attribute\HttpRequest;
use Retrofit\Attribute\Path;
use Retrofit\Internal\ParameterHandler\ParameterHandler;
use Retrofit\Internal\ParameterHandler\PathParameterHandler;

readonly class PathParameterHandlerFactory extends AbstractParameterHandlerFactory
{
    public function create(Path $param, HttpRequest $httpRequest, ReflectionMethod $reflectionMethod, int $position): ParameterHandler
    {
        return new PathParameterHandler($param->name(), $param->encoded(), $this->converterProvider->getStringConverter(), $reflectionMethod, $position);
    }
}
