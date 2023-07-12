<?php
declare(strict_types=1);

namespace Retrofit\Internal\ParameterHandler\Factory;

use ReflectionMethod;
use Retrofit\Attribute\HttpRequest;
use Retrofit\Attribute\QueryName;
use Retrofit\Internal\Encoding;
use Retrofit\Internal\ParameterHandler\ParameterHandler;
use Retrofit\Internal\ParameterHandler\QueryNameParameterHandler;
use Retrofit\Type;

readonly class QueryNameParameterHandlerFactory extends AbstractParameterHandlerFactory
{
    public function create(
        QueryName $param,
        HttpRequest $httpRequest,
        ?Encoding $encoding,
        ReflectionMethod $reflectionMethod,
        int $position,
        Type $type
    ): ParameterHandler
    {
        return new QueryNameParameterHandler($param->encoded(), $this->converterProvider->getStringConverter(), $reflectionMethod, $position);
    }
}
