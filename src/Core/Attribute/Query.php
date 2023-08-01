<?php

declare(strict_types=1);

namespace Retrofit\Core\Attribute;

use Attribute;

/**
 * Query parameter appended to the URL.
 *
 * Values are converted to strings using {@link ConverterFactory::stringConverter())} and then URL encoded.
 * <code>null</code> values are ignored. Passing an array will result in a query parameter for each non-null item.
 *
 * Simple Example:
 * <pre>
 * #[GET('/friends')]
 * public function friends(#[Query('page')] int $page): Call;
 * </pre>
 *
 * Calling with <code>$foo->friends(1)</code> yields <code>/friends?page=1</code>.
 *
 * Example with null:
 * <pre>
 * #[GET('/friends')]
 * public function friends(#[Query('group')] ?int $group): Call;
 * </pre>
 *
 * Calling with <code>$foo->friends(null)</code> yields <code>/friends</code>.
 *
 * Array/Varargs Example:
 * <pre>
 * #[GET('/friends')]
 * public function friends(#[Query('group')] string... $groups): Call;
 * </pre>
 *
 * Calling with <code>$foo->friends('coworker', 'bowling')</code> yields <code>/friends?group=coworker&group=bowling</code>.
 *
 * Parameter names and values are URL encoded by default. Specify <code>encoded=true</code> to change this behavior.
 * <pre>
 * #[GET('/friends')]
 * public function friends(#[Query('group', true)] string... $groups): Call;
 * </pre>
 *
 * Calling with <code>$foo->friends('foo+bar'))</code> yields <code>/friends?group=foo+bar</code>.
 *
 * @see QueryMap
 * @see QueryName
 *
 * @api
 */
#[Attribute(Attribute::TARGET_PARAMETER)]
readonly class Query implements ParameterAttribute
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
