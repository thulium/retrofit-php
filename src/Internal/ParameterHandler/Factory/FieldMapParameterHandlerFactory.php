<?php
declare(strict_types=1);

namespace Retrofit\Internal\ParameterHandler\Factory;

use ReflectionMethod;
use Retrofit\Attribute\FieldMap;
use Retrofit\Attribute\HttpRequest;
use Retrofit\Internal\Encoding;
use Retrofit\Internal\ParameterHandler\FieldMapParameterHandler;
use Retrofit\Internal\ParameterHandler\ParameterHandler;
use Retrofit\Internal\Utils\Utils;
use Retrofit\Type;

readonly class FieldMapParameterHandlerFactory extends AbstractParameterHandlerFactory
{
    public function create(
        FieldMap $param,
        HttpRequest $httpRequest,
        ?Encoding $encoding,
        ReflectionMethod $reflectionMethod,
        int $position,
        Type $type
    ): ParameterHandler
    {
        if ($encoding !== Encoding::FORM_URL_ENCODED) {
            throw Utils::parameterException($reflectionMethod, $position, '#[FieldMap] parameters can only be used with form encoding.');
        }

        return new FieldMapParameterHandler($param->encoded(), $this->converterProvider->getStringConverter(), $reflectionMethod, $position);
    }
}
