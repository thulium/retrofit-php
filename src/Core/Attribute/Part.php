<?php

declare(strict_types=1);

namespace Retrofit\Core\Attribute;

use Attribute;
use Retrofit\Core\Converter\Converter;
use Retrofit\Core\MimeEncoding;
use Retrofit\Core\Multipart\PartInterface;

/**
 * Denotes a single part of a multi-part request.
 *
 * The parameter type on which this annotation exists will be processed in one of two ways:
 *
 * <ul>
 *     <li>If the type is {@link PartInterface} the contents will be used directly. Omit the name from the attribute
 *         (<code>#[Part] PartInterface $part</code>).</li>
 *     <li>Other object types will be converted to an appropriate representation by using a {@link Converter converter}.
 *         Supply the part name in the attribute (<code>#[Part('foo')] Image $photo</code>).</li>
 * </ul>
 *
 * Values may be null which will omit them from the request body.
 *
 * <pre>
 * #[Multipart]
 * #[POST('/')]
 * public function example(
 *     #[Part('description')] string $description,
 *     #[Part('image', MimeEncoding::BIT_8) Image $image
 * ): Call;
 * </pre>
 *
 * Part parameters may not be null.
 *
 * @see Multipart
 * @see PartMap
 *
 * @api
 */
#[Attribute(Attribute::TARGET_PARAMETER)]
readonly class Part implements ParameterAttribute
{
    private MimeEncoding $encoding;

    public function __construct(
        private ?string $name = null,
        MimeEncoding|string $encoding = MimeEncoding::BINARY,
    )
    {
        $this->encoding = MimeEncoding::fromEnumOrString($encoding);
    }

    public function name(): ?string
    {
        return $this->name;
    }

    public function encoding(): MimeEncoding
    {
        return $this->encoding;
    }
}
