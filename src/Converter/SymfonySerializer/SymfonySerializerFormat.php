<?php

declare(strict_types=1);

namespace Retrofit\Converter\SymfonySerializer;

enum SymfonySerializerFormat: string
{
    case JSON = 'json';
    case XML = 'xml';
    case YAML = 'yaml';
}
