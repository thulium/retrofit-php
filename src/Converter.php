<?php
declare(strict_types=1);

namespace Retrofit;

interface Converter
{
    public function convert(mixed $value): mixed;
}
