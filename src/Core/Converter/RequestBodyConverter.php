<?php

declare(strict_types=1);

namespace Retrofit\Core\Converter;

use Psr\Http\Message\StreamInterface;

interface RequestBodyConverter extends Converter
{
    public function convert(mixed $value): StreamInterface;
}
