<?php
declare(strict_types=1);

namespace Retrofit\Internal\ParameterHandler\Factory;

use ReflectionMethod;
use Retrofit\Attribute\HttpRequest;
use Retrofit\Attribute\Part;
use Retrofit\Internal\Encoding;
use Retrofit\Internal\ParameterHandler\ParameterHandler;
use Retrofit\Internal\ParameterHandler\PartParameterHandler;
use Retrofit\Internal\Utils\Utils;

readonly class PartParameterHandlerFactory extends AbstractParameterHandlerFactory
{
    public function create(Part $param, HttpRequest $httpRequest, ?Encoding $encoding, ReflectionMethod $reflectionMethod, int $position): ParameterHandler
    {
        if ($encoding !== Encoding::MULTIPART) {
            throw Utils::parameterException($reflectionMethod, $position, '#[Part] parameters can only be used with multipart.');
        }

        $converter = $this->converterProvider->getRequestBodyConverter();

        return new PartParameterHandler($param->name(), $param->encoding(), $converter, $reflectionMethod, $position);
    }
}
