<?php

declare(strict_types=1);

namespace Retrofit\Converter\SymfonySerializer;

/**
 * Convenient enum for all supported Symfony Serializer formats.
 *
 * @api
 */
enum SymfonySerializerFormat: string
{
    case JSON = 'json';
    case XML = 'xml';
    case YAML = 'yaml';
}
