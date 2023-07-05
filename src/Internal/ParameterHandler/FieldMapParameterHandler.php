<?php
declare(strict_types=1);

namespace Retrofit\Internal\ParameterHandler;

use ReflectionMethod;
use Retrofit\Converter;
use Retrofit\Internal\RequestBuilder;
use Retrofit\Internal\Utils\Utils;

readonly class FieldMapParameterHandler implements ParameterHandler
{
    use WithMapParameter;

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
            throw Utils::parameterException($this->reflectionMethod, $this->position, 'Field map was null.');
        }

        $this->validateAndApply($value, 'Field', function (string|array $entryKey, string|array|null $entryValue) use ($requestBuilder): void {
            $requestBuilder->addFormField($entryKey, $entryValue, $this->encoded);
        });
    }
}
