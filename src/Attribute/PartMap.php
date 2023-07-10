<?php
declare(strict_types=1);

namespace Retrofit\Attribute;

use Attribute;
use Retrofit\HttpMethod;
use Retrofit\MimeEncoding;

#[Attribute(Attribute::TARGET_PARAMETER)]
readonly class PartMap
{
    private MimeEncoding $encoding;

    public function __construct(MimeEncoding|string $encoding = MimeEncoding::BINARY)
    {
        $this->encoding = is_string($encoding) ? HttpMethod::from($encoding) : $encoding;
    }

    public function encoding(): MimeEncoding
    {
        return $this->encoding;
    }
}
