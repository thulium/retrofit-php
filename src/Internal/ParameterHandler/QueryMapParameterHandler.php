<?php
declare(strict_types=1);

namespace Retrofit\Internal\ParameterHandler;

use Ouzo\Utilities\Strings;
use ReflectionMethod;
use Retrofit\Converter;
use Retrofit\Internal\RequestBuilder;
use Retrofit\Internal\Utils\Utils;

readonly class QueryMapParameterHandler implements ParameterHandler
{
    use WithQueryParameter;

    public function __construct(
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

        if (!is_array($value)) {
            throw Utils::parameterException($this->reflectionMethod, $this->position, 'Parameter should be an array.');
        }

        foreach ($value as $entryKey => $entryValue) {
            if (Strings::isBlank($entryKey)) {
                throw Utils::parameterException($this->reflectionMethod, $this->position, 'Query map contained empty key.');
            }
            if (is_null($entryValue)) {
                throw Utils::parameterException($this->reflectionMethod, $this->position,
                    "Query map contained null value for key '{$entryKey}'.");
            }

            $entryValue = $this->converter->convert($entryValue);
            $requestBuilder->addQueryParam($entryKey, $entryValue, $this->encoded);
        }
    }
}
