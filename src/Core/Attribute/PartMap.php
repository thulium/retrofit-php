<?php
declare(strict_types=1);

namespace Retrofit\Core\Attribute;

use Attribute;
use Retrofit\Core\MimeEncoding;

#[Attribute(Attribute::TARGET_PARAMETER)]
readonly class PartMap
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
