<?php
declare(strict_types=1);

namespace Retrofit\Internal\ParameterHandler\Factory;

use ReflectionMethod;
use Retrofit\Attribute\HttpRequest;
use Retrofit\Attribute\QueryMap;
use Retrofit\Internal\Encoding;
use Retrofit\Internal\ParameterHandler\ParameterHandler;
use Retrofit\Internal\ParameterHandler\QueryMapParameterHandler;

readonly class QueryMapParameterHandlerFactory extends AbstractParameterHandlerFactory
{
    public function create(QueryMap $param, HttpRequest $httpRequest, ?Encoding $encoding, ReflectionMethod $reflectionMethod, int $position): ParameterHandler
    {
        return new QueryMapParameterHandler($param->encoded(), $this->converterProvider->getStringConverter(), $reflectionMethod, $position);
    }
}
