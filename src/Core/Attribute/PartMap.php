<?php

declare(strict_types=1);

namespace Retrofit\Core\Attribute;

use Attribute;
use Retrofit\Core\Converter\Converter;
use Retrofit\Core\MimeEncoding;
use Retrofit\Core\Multipart\PartInterface;

/**
 * Denotes name and value parts of a multi-part request.
 *
 * Values of the map on which this annotation exists will be processed in one of two ways:
 *
 * <ul>
 *     <li>If the type is {@link PartInterface} the value will be used directly with its content type.</li>
 *     <li>Other object types will be converted to an appropriate representation by using a {@link Converter converter}.</li>
 * </ul>
 *
 * <pre>
 * #[Multipart]
 * #[POST('/upload')]
 * public function upload(
 *     #[Part('file')] PartInterface $file,
 *     #[PartMap] array $params
 * ): Call;
 * </pre>
 *
 * <code>null</code> value for the map, as a key, or as a value is not allowed.
 *
 * @see Multipart
 * @see Part
 *
 * @api
 */
#[Attribute(Attribute::TARGET_PARAMETER)]
readonly class PartMap implements ParameterAttribute
{
    private MimeEncoding $encoding;

    public function __construct(MimeEncoding|string $encoding = MimeEncoding::BINARY)
    {
        $this->encoding = MimeEncoding::fromEnumOrString($encoding);
    }

    public function encoding(): MimeEncoding
    {
        return $this->encoding;
    }
}
