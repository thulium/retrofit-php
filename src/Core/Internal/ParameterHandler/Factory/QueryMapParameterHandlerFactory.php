<?php

declare(strict_types=1);

namespace Retrofit\Core\Internal\ParameterHandler\Factory;

use ReflectionMethod;
use Retrofit\Core\Attribute\HttpRequest;
use Retrofit\Core\Attribute\QueryMap;
use Retrofit\Core\Internal\Encoding;
use Retrofit\Core\Internal\ParameterHandler\ParameterHandler;
use Retrofit\Core\Internal\ParameterHandler\QueryMapParameterHandler;
use Retrofit\Core\Type;

readonly class QueryMapParameterHandlerFactory extends AbstractParameterHandlerFactory
{
    public function create(
        QueryMap $param,
        HttpRequest $httpRequest,
        ?Encoding $encoding,
        ReflectionMethod $reflectionMethod,
        int $position,
        Type $type,
    ): ParameterHandler
    {
        return new QueryMapParameterHandler($param->encoded(), $this->converterProvider->getStringConverter($type), $reflectionMethod, $position);
    }
}
