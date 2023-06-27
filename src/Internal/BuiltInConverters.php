<?php
declare(strict_types=1);

namespace Retrofit\Internal;

use Retrofit\Converter;
use Retrofit\ConverterFactory;

class BuiltInConverters implements ConverterFactory
{
    public function requestBodyConverter(): ?Converter
    {
        return null;
    }

    public function responseBodyConverter(): ?Converter
    {
        return null;
    }

    public function stringConverter(): ?Converter
    {
        return self::toStringConverter();
    }

    public static function toStringConverter(): Converter
    {
        return new class implements Converter {
            public function convert(mixed $value): string
            {
                // if it's an array or object, just serialize it
                if (is_array($value) || is_object($value)) {
                    return serialize($value);
                }

                if ($value === true) {
                    return 'true';
                }

                if ($value === false) {
                    return 'false';
                }

                return (string)$value;
            }
        };
    }
}
