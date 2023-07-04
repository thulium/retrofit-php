<?php
declare(strict_types=1);

namespace Retrofit\Internal\ParameterHandler\Factory;

use ReflectionMethod;
use Retrofit\Attribute\HeaderMap;
use Retrofit\Attribute\HttpRequest;
use Retrofit\Internal\ParameterHandler\HeaderMapParameterHandler;
use Retrofit\Internal\ParameterHandler\ParameterHandler;

readonly class HeaderMapParameterHandlerFactory extends AbstractParameterHandlerFactory
{
    public function create(HeaderMap $param, HttpRequest $httpRequest, ReflectionMethod $reflectionMethod, int $position): ParameterHandler
    {
        return new HeaderMapParameterHandler($this->converterProvider->getStringConverter(), $reflectionMethod, $position);
    }
}
