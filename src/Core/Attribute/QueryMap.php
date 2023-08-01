<?php

declare(strict_types=1);

namespace Retrofit\Core\Attribute;

use Attribute;

/**
 * Query parameter keys and values appended to the URL.
 *
 * Values are converted to strings using {@link ConverterFactory::stringConverter())}.
 *
 * Simple Example:
 * <pre>
 * #[GET('/friends')]
 * public function friends(#[QueryMap] array $filters): Call;
 * </pre>
 *
 * Calling with <code>$foo->friends(['group' => 'coworker', 'age' => 42])</code> yields <code>/friends?group=coworker&age=42</code>.
 *
 * Map keys and values representing parameter values are URL encoded by default. Specify <code>encoded=true</code> to change this behavior.
 * <pre>
 * #[GET('/friends')]
 * public function friends(#[QueryMap(true)] array $filters): Call;
 * </pre>
 *
 * Calling with <code>$foo->list(['group' => 'coworker+bowling']) yields <code>/friends?group=coworker+bowling</code>.
 *
 * A <code>null</code> value for the map, as a key, or as a value is not allowed.
 *
 * @see Query
 * @see QueryName
 *
 * @api
 */
#[Attribute(Attribute::TARGET_PARAMETER)]
readonly class QueryMap implements ParameterAttribute
{
    public function __construct(private bool $encoded = false)
    {
    }

    public function encoded(): bool
    {
        return $this->encoded;
    }
}
