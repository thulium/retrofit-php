<?php

declare(strict_types=1);

namespace Retrofit\Core\Attribute;

use Attribute;
use Retrofit\Core\Converter\ConverterFactory;

/**
 * Named pair for a form-encoded request.
 *
 * Values are converted to strings using {@link ConverterFactory::stringConverter()) and then form URL encoded.
 * <code>null</code> values are ignored. Passing an array will result in a field pair for each non-null item.
 *
 * Simple Example:
 * <pre>
 * #[FormUrlEncoded]
 * #[POST('/']
 * public function example(
 *     #[Field('name')] string $name,
 *     #[Field('occupation')] string $occupation
 * ): Call;
 * </pre>
 *
 * Calling with <code>$foo->example('Bob Smith', 'President')</code> yields a request body of
 * <code>name=Bob+Smith&occupation=President</code>.
 *
 * Array/Varargs Example:
 * <pre>
 * #[FormUrlEncoded]
 * #[POST('/list']
 * public function example(#[Field('name')] string... $names): Call;
 * </pre>
 *
 * Calling with <code>foo->example('Bob Smith', 'Jane Doe')</code> yields a request body of
 * <code>name=Bob+Smith&name=Jane+Doe</code>.
 *
 * @see FormUrlEncoded
 * @see FieldMap
 *
 * @api
 */
#[Attribute(Attribute::TARGET_PARAMETER)]
readonly class Field implements ParameterAttribute
{
    public function __construct(
        private string $name,
        private bool $encoded = false,
    )
    {
    }

    public function name(): string
    {
        return $this->name;
    }

    public function encoded(): bool
    {
        return $this->encoded;
    }
}
