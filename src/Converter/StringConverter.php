<?php
declare(strict_types=1);

namespace Retrofit\Converter;

interface StringConverter extends Converter
{
    public function convert(mixed $value): string;
}
