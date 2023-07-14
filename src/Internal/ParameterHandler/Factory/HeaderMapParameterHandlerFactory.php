<?php
declare(strict_types=1);

namespace Retrofit\Internal\ParameterHandler\Factory;

use ReflectionMethod;
use Retrofit\Attribute\HeaderMap;
use Retrofit\Attribute\HttpRequest;
use Retrofit\Internal\Encoding;
use Retrofit\Internal\ParameterHandler\HeaderMapParameterHandler;
use Retrofit\Internal\ParameterHandler\ParameterHandler;
use Retrofit\Type;

readonly class HeaderMapParameterHandlerFactory extends AbstractParameterHandlerFactory
{
    public function create(
        HeaderMap $param,
        HttpRequest $httpRequest,
        ?Encoding $encoding,
        ReflectionMethod $reflectionMethod,
        int $position,
        Type $type
    ): ParameterHandler
    {
        return new HeaderMapParameterHandler($this->converterProvider->getStringConverter($type), $reflectionMethod, $position);
    }
}
