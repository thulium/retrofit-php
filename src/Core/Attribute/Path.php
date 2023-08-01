<?php

declare(strict_types=1);

namespace Retrofit\Core\Attribute;

use Attribute;

/**
 * Named replacement in a URL path segment.
 *
 * Values are converted to strings using {@link ConverterFactory::stringConverter())},
 * if no matching string converter is installed) and then URL encoded.
 *
 * Simple example:
 *
 * <pre>
 * #[GET('/image/{id}')]
 * public function example(#[Path('id')] int $id): Call;
 * </pre>
 *
 * Calling with <code>$foo->example(1)</code> yields /image/1.
 *
 * Values are URL encoded by default. Disable with <code>encoded=true</code>.
 *
 * <pre>
 * #[GET('/user/{name}')]
 * public function encoded(#[Path('name')] string $name): Call;
 *
 * #[GET('/user/{name}')]
 * public function notEncoded(#[Path('name', false)] string $name): Call;
 * </pre>
 *
 * Calling <code>$foo->encoded("John+Doe")</code> yields <code>/user/John%2BDoe</code>
 * whereas <code>$foo->notEncoded("John+Doe")</code> yields <code>/user/John+Doe</code>.
 *
 * Path parameters may not be null.
 *
 * @api
 */
#[Attribute(Attribute::TARGET_PARAMETER)]
readonly class Path implements ParameterAttribute
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
