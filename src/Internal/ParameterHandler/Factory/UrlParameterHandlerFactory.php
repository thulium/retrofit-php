<?php
declare(strict_types=1);

namespace Retrofit\Internal\ParameterHandler\Factory;

use ReflectionMethod;
use Retrofit\Attribute\Url;
use Retrofit\Internal\ParameterHandler\ParameterHandler;
use Retrofit\Internal\ParameterHandler\UrlParameterHandler;

readonly class UrlParameterHandlerFactory extends AbstractParameterHandlerFactory
{
    public function create(Url $path, ReflectionMethod $reflectionMethod, int $position): ParameterHandler
    {
        return new UrlParameterHandler($reflectionMethod, $position);
    }
}
