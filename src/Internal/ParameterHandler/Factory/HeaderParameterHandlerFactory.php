<?php
declare(strict_types=1);

namespace Retrofit\Internal\ParameterHandler\Factory;

use ReflectionMethod;
use Retrofit\Attribute\Header;
use Retrofit\Attribute\HttpRequest;
use Retrofit\Internal\ParameterHandler\HeaderParameterHandler;
use Retrofit\Internal\ParameterHandler\ParameterHandler;

readonly class HeaderParameterHandlerFactory extends AbstractParameterHandlerFactory
{
    public function create(Header $param, HttpRequest $httpRequest, ReflectionMethod $reflectionMethod, int $position): ParameterHandler
    {
        return new HeaderParameterHandler($param->name(), $this->converterProvider->getStringConverter());
    }
}
