<?php
declare(strict_types=1);

namespace Retrofit\Converter;

use Psr\Http\Message\StreamInterface;

interface ResponseBodyConverter extends Converter
{
    public function convert(StreamInterface $value): mixed;
}
