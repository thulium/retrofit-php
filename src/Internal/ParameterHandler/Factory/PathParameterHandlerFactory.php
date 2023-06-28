<?php
declare(strict_types=1);

namespace Retrofit\Internal\ParameterHandler\Factory;

use ReflectionMethod;
use Retrofit\Attribute\Path;
use Retrofit\Internal\ParameterHandler\ParameterHandler;
use Retrofit\Internal\ParameterHandler\PathParameterHandler;
use Retrofit\Internal\Utils\Utils;

readonly class PathParameterHandlerFactory extends AbstractParameterHandlerFactory
{
    public function create(Path $path, ReflectionMethod $reflectionMethod, int $position): ParameterHandler
    {
        $name = $path->name();
        if (!in_array($name, $this->httpRequest->pathParameters())) {
            throw Utils::parameterException($reflectionMethod, $position, "URL '{$this->httpRequest->path()}' does not contain '{$name}'.");
        }

        $converter = $this->converterProvider->getStringConverter();
        return new PathParameterHandler($name, $path->encoded(), $converter, $reflectionMethod, $position);
    }
}