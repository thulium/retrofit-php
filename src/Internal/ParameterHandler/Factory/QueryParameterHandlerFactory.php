<?php
declare(strict_types=1);

namespace Retrofit\Internal\ParameterHandler\Factory;

use ReflectionMethod;
use Retrofit\Attribute\HttpRequest;
use Retrofit\Attribute\Query;
use Retrofit\Internal\ParameterHandler\ParameterHandler;
use Retrofit\Internal\ParameterHandler\QueryParameterHandler;

readonly class QueryParameterHandlerFactory extends AbstractParameterHandlerFactory
{
    public function create(Query $param, HttpRequest $httpRequest, ReflectionMethod $reflectionMethod, int $position): ParameterHandler
    {
        return new QueryParameterHandler($param->name(), $param->encoded(), $this->converterProvider->getStringConverter(), $reflectionMethod, $position);
    }
}
