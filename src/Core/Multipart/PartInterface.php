<?php

declare(strict_types=1);

namespace Retrofit\Core\Multipart;

use Psr\Http\Message\StreamInterface;

interface PartInterface
{
    public function getName(): string;

    public function getBody(): StreamInterface|string;

    public function getFilename(): ?string;

    /**
     * @return array<string, string>
     */
    public function getHeaders(): array;
}
