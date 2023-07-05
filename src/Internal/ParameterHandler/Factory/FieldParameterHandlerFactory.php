<?php
declare(strict_types=1);

namespace Retrofit\Internal\ParameterHandler\Factory;

use ReflectionMethod;
use Retrofit\Attribute\Field;
use Retrofit\Attribute\HttpRequest;
use Retrofit\Internal\Encoding;
use Retrofit\Internal\ParameterHandler\FieldParameterHandler;
use Retrofit\Internal\ParameterHandler\ParameterHandler;
use Retrofit\Internal\Utils\Utils;

readonly class FieldParameterHandlerFactory extends AbstractParameterHandlerFactory
{
    public function create(Field $param, HttpRequest $httpRequest, ?Encoding $encoding, ReflectionMethod $reflectionMethod, int $position): ParameterHandler
    {
        if ($encoding !== Encoding::FORM_URL_ENCODED) {
            throw Utils::parameterException($reflectionMethod, $position, '#[Field] parameters can only be used with form encoding.');
        }

        return new FieldParameterHandler($param->name(), $param->encoded(), $this->converterProvider->getStringConverter());
    }
}
