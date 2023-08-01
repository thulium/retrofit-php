<?php

declare(strict_types=1);

namespace Retrofit\Core\Attribute;

use Attribute;

/**
 * Named key/value pairs for a form-encoded request.
 *
 * Simple Example:
 * <pre>
 * #[FormUrlEncoded]
 * #[POST('/things']
 * public function things(#[FieldMap] array $fields): Call;
 * </pre>
 *
 * Calling with <code>$foo->things(['foo' => 'bar', 'kit' => 'kat'])</code> yields a request body of <code>foo=bar&kit=kat</code>.
 * <code>null</code> value for the map, as a key, or as a value is not allowed.
 *
 * @see FormUrlEncoded
 * @see Field
 *
 * @api
 */
#[Attribute(Attribute::TARGET_PARAMETER)]
readonly class FieldMap implements ParameterAttribute
{
    public function __construct(private bool $encoded = false)
    {
    }

    public function encoded(): bool
    {
        return $this->encoded;
    }
}
