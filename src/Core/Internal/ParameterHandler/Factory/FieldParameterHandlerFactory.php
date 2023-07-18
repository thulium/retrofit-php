<?php

declare(strict_types=1);

namespace Retrofit\Core\Internal\ParameterHandler\Factory;

use ReflectionMethod;
use Retrofit\Core\Attribute\Field;
use Retrofit\Core\Attribute\HttpRequest;
use Retrofit\Core\Attribute\ParameterAttribute;
use Retrofit\Core\Internal\Encoding;
use Retrofit\Core\Internal\ParameterHandler\FieldParameterHandler;
use Retrofit\Core\Internal\ParameterHandler\ParameterHandler;
use Retrofit\Core\Internal\Utils\Utils;
use Retrofit\Core\Type;

/**
 * @extends AbstractParameterHandlerFactory<Field>
 */
readonly class FieldParameterHandlerFactory extends AbstractParameterHandlerFactory
{
    /**
     * @param Field $param
     * @param HttpRequest $httpRequest
     * @param Encoding|null $encoding
     * @param ReflectionMethod $reflectionMethod
     * @param int $position
     * @param Type $type
     * @return ParameterHandler
     */
    public function create(
        ParameterAttribute $param,
        HttpRequest $httpRequest,
        ?Encoding $encoding,
        ReflectionMethod $reflectionMethod,
        int $position,
        Type $type,
    ): ParameterHandler
    {
        if ($encoding !== Encoding::FORM_URL_ENCODED) {
            throw Utils::parameterException($reflectionMethod, $position, '#[Field] parameters can only be used with form encoding.');
        }

        return new FieldParameterHandler($param->name(), $param->encoded(), $this->converterProvider->getStringConverter($type));
    }
}
