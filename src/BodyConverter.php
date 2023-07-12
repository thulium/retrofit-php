<?php
declare(strict_types=1);

namespace Retrofit;

use Psr\Http\Message\StreamInterface;

interface BodyConverter extends Converter
{
    public function convert(mixed $value): StreamInterface;
}
