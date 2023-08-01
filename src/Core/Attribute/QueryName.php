<?php

declare(strict_types=1);

namespace Retrofit\Core\Attribute;

use Attribute;

/**
 * Query parameter appended to the URL that has no value.
 * Passing an array will result in a query parameter for each non-null item.
 *
 * Simple Example:
 * <pre>
 * #[GET('/friends')]
 * public function friends(#[QueryName] string $filter): Call;
 * </pre>
 *
 * Calling with <code>$foo->friends('contains(Bob)')</code> yields <code>/friends?contains(Bob)</code>.
 *
 * Array/Varargs Example:
 * <pre>
 * #[GET('/friends')]
 * public function friends(#[QueryName] string... $filter): Call;
 * </pre>
 *
 * Calling with <code>$foo->friends('contains(Bob)', 'age(42)')</code> yields <code>/friends?contains(Bob)&age(42)</code>.
 *
 * Parameter names are URL encoded by default. Specify <code>encoded=true</code> to change this behavior.
 * <pre>
 * #[GET('/friends')]
 * public function friends(#[QueryName(true)] string... $filter): Call;
 * </pre>
 *
 * Calling with <code>$foo->friends('name+age'))</code> yields <code>/friends?name+age</code>.
 *
 * @see Query
 * @see QueryMap
 *
 * @api
 */
#[Attribute(Attribute::TARGET_PARAMETER)]
readonly class QueryName implements ParameterAttribute
{
    public function __construct(private bool $encoded = false)
    {
    }

    public function encoded(): bool
    {
        return $this->encoded;
    }
}
