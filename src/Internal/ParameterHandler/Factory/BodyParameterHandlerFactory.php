<?php
declare(strict_types=1);

namespace Retrofit\Internal\ParameterHandler\Factory;

use ReflectionMethod;
use Retrofit\Attribute\Body;
use Retrofit\Attribute\HttpRequest;
use Retrofit\Internal\Encoding;
use Retrofit\Internal\ParameterHandler\BodyParameterHandler;
use Retrofit\Internal\ParameterHandler\ParameterHandler;
use Retrofit\Type;

readonly class BodyParameterHandlerFactory extends AbstractParameterHandlerFactory
{
    public function create(
        Body $param,
        HttpRequest $httpRequest,
        ?Encoding $encoding,
        ReflectionMethod $reflectionMethod,
        int $position,
        Type $type
    ): ParameterHandler
    {
        $converter = $this->converterProvider->getRequestBodyConverter($type);

        return new BodyParameterHandler($converter, $reflectionMethod, $position);
    }
}
