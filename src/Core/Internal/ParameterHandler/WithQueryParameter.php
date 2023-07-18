<?php

declare(strict_types=1);

namespace Retrofit\Core\Internal\ParameterHandler;

use Ouzo\Utilities\Arrays;
use Retrofit\Core\Internal\Utils\Utils;

trait WithQueryParameter
{
    protected function validateAndConvert(mixed $value): mixed
    {
        if (is_array($value)) {
            if (!array_is_list($value)) {
                throw Utils::parameterException(
                    $this->reflectionMethod,
                    $this->position,
                    'Parameter must be a list.',
                );
            }

            if (Arrays::any($value, fn(mixed $v): bool => is_object($v))) {
                throw Utils::parameterException(
                    $this->reflectionMethod,
                    $this->position,
                    'One of the list value is an object.',
                );
            }

            return Arrays::map($value, fn(mixed $v): string => $this->converter->convert($v));
        }

        return $this->converter->convert($value);
    }
}
