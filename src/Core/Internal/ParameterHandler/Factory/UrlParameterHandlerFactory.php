<?php

declare(strict_types=1);

namespace Retrofit\Core\Internal\ParameterHandler\Factory;

use ReflectionMethod;
use Retrofit\Core\Attribute\HttpRequest;
use Retrofit\Core\Attribute\Url;
use Retrofit\Core\Internal\Encoding;
use Retrofit\Core\Internal\ParameterHandler\ParameterHandler;
use Retrofit\Core\Internal\ParameterHandler\UrlParameterHandler;
use Retrofit\Core\Type;

readonly class UrlParameterHandlerFactory extends AbstractParameterHandlerFactory
{
    public function create(
        Url $param,
        HttpRequest $httpRequest,
        ?Encoding $encoding,
        ReflectionMethod $reflectionMethod,
        int $position,
        Type $type,
    ): ParameterHandler {
        return new UrlParameterHandler($reflectionMethod, $position);
    }
}
