<?php
declare(strict_types=1);

namespace Retrofit\Internal\ParameterHandler;

use Ouzo\Utilities\Arrays;
use ReflectionMethod;
use Retrofit\Converter;
use Retrofit\Internal\RequestBuilder;
use Retrofit\Internal\Utils\Utils;

readonly class QueryParameterHandler implements ParameterHandler
{
    public function __construct(
        private string $name,
        private bool $encoded,
        private Converter $converter,
        private ReflectionMethod $reflectionMethod,
        private int $position
    )
    {
    }

    public function apply(RequestBuilder $requestBuilder, mixed $value): void
    {
        if (is_null($value)) {
            return;
        }

        if (is_array($value)) {
            if (!array_is_list($value)) {
                throw Utils::parameterException($this->reflectionMethod, $this->position,
                    'Parameter must be a list.');
            }

            if (Arrays::any($value, fn(mixed $v): bool => is_object($v))) {
                throw Utils::parameterException($this->reflectionMethod, $this->position,
                    'One of the list value is an object.');
            }

            $value = collect($value)
                ->map(fn(mixed $v): string => $this->converter->convert($v))
                ->all();
        } else {
            $value = $this->converter->convert($value);
        }

        $requestBuilder->addQueryParam($this->name, $value, $this->encoded);
    }
}
