<?php
declare(strict_types=1);

namespace Retrofit\Internal\ParameterHandler\Factory;

use ReflectionMethod;
use Retrofit\Attribute\HttpRequest;
use Retrofit\Attribute\PartMap;
use Retrofit\Internal\Encoding;
use Retrofit\Internal\ParameterHandler\ParameterHandler;
use Retrofit\Internal\ParameterHandler\PartMapParameterHandler;
use Retrofit\Internal\Utils\Utils;
use Retrofit\Type;

readonly class PartMapParameterHandlerFactory extends AbstractParameterHandlerFactory
{
    public function create(
        PartMap $param,
        HttpRequest $httpRequest,
        ?Encoding $encoding,
        ReflectionMethod $reflectionMethod,
        int $position,
        Type $type
    ): ParameterHandler
    {
        if ($encoding !== Encoding::MULTIPART) {
            throw Utils::parameterException($reflectionMethod, $position, '#[PartMap] parameters can only be used with multipart.');
        }

        $converter = $this->converterProvider->getRequestBodyConverter();

        return new PartMapParameterHandler($param->encoding(), $converter, $reflectionMethod, $position);
    }
}
