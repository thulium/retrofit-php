<?php

declare(strict_types=1);

namespace Retrofit\Core\Internal\ParameterHandler\Factory;

use ReflectionMethod;
use Retrofit\Core\Attribute\Header;
use Retrofit\Core\Attribute\HttpRequest;
use Retrofit\Core\Internal\Encoding;
use Retrofit\Core\Internal\ParameterHandler\HeaderParameterHandler;
use Retrofit\Core\Internal\ParameterHandler\ParameterHandler;
use Retrofit\Core\Type;

readonly class HeaderParameterHandlerFactory extends AbstractParameterHandlerFactory
{
    public function create(
        Header $param,
        HttpRequest $httpRequest,
        ?Encoding $encoding,
        ReflectionMethod $reflectionMethod,
        int $position,
        Type $type,
    ): ParameterHandler {
        return new HeaderParameterHandler($param->name(), $this->converterProvider->getStringConverter($type));
    }
}
