<?php
declare(strict_types=1);

namespace Retrofit\Multipart;

use Psr\Http\Message\StreamInterface;

interface PartInterface
{
    public function getName(): string;

    public function getBody(): StreamInterface|string;

    public function getFilename(): ?string;

    public function getHeaders(): array;

    public static function createFromData(string $name, StreamInterface|string $body);
}
