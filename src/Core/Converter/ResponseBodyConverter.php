<?php

declare(strict_types=1);

namespace Retrofit\Core\Converter;

use Psr\Http\Message\StreamInterface;

interface ResponseBodyConverter extends Converter
{
    public function convert(StreamInterface $value): mixed;
}
