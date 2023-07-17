<?php

declare(strict_types=1);

namespace Retrofit\Core\Internal\ParameterHandler\Factory;

use ReflectionMethod;
use Retrofit\Core\Attribute\HttpRequest;
use Retrofit\Core\Attribute\Query;
use Retrofit\Core\Internal\Encoding;
use Retrofit\Core\Internal\ParameterHandler\ParameterHandler;
use Retrofit\Core\Internal\ParameterHandler\QueryParameterHandler;
use Retrofit\Core\Type;

readonly class QueryParameterHandlerFactory extends AbstractParameterHandlerFactory
{
    public function create(
        Query $param,
        HttpRequest $httpRequest,
        ?Encoding $encoding,
        ReflectionMethod $reflectionMethod,
        int $position,
        Type $type,
    ): ParameterHandler {
        return new QueryParameterHandler($param->name(), $param->encoded(), $this->converterProvider->getStringConverter($type), $reflectionMethod, $position);
    }
}
