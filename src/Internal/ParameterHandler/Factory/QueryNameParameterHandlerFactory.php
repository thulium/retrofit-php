<?php
declare(strict_types=1);

namespace Retrofit\Internal\ParameterHandler\Factory;

use ReflectionMethod;
use Retrofit\Attribute\HttpRequest;
use Retrofit\Attribute\QueryName;
use Retrofit\Internal\ParameterHandler\ParameterHandler;
use Retrofit\Internal\ParameterHandler\QueryNameParameterHandler;

readonly class QueryNameParameterHandlerFactory extends AbstractParameterHandlerFactory
{
    public function create(QueryName $param, HttpRequest $httpRequest, ReflectionMethod $reflectionMethod, int $position): ParameterHandler
    {
        return new QueryNameParameterHandler($param->encoded(), $this->converterProvider->getStringConverter(), $reflectionMethod, $position);
    }
}
