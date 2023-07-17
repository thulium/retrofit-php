<?php

declare(strict_types=1);

namespace Retrofit\Core\Internal\ParameterHandler\Factory;

use ReflectionMethod;
use Retrofit\Core\Attribute\HeaderMap;
use Retrofit\Core\Attribute\HttpRequest;
use Retrofit\Core\Internal\Encoding;
use Retrofit\Core\Internal\ParameterHandler\HeaderMapParameterHandler;
use Retrofit\Core\Internal\ParameterHandler\ParameterHandler;
use Retrofit\Core\Type;

readonly class HeaderMapParameterHandlerFactory extends AbstractParameterHandlerFactory
{
    public function create(
        HeaderMap $param,
        HttpRequest $httpRequest,
        ?Encoding $encoding,
        ReflectionMethod $reflectionMethod,
        int $position,
        Type $type,
    ): ParameterHandler {
        return new HeaderMapParameterHandler($this->converterProvider->getStringConverter($type), $reflectionMethod, $position);
    }
}
