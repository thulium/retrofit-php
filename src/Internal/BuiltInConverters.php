<?php
declare(strict_types=1);

namespace Retrofit\Internal;

use Retrofit\Converter;

readonly class BuiltInConverters
{
    public static function JsonEncodeRequestBodyConverter(): Converter
    {
        return new class implements Converter {
            public function convert(mixed $value): string
            {
                return json_encode($value);
            }
        };
    }

    public static function ToStringConverter(): Converter
    {
        return new class implements Converter {
            public function convert(mixed $value): string
            {
                // If it's an array or object, just serialize it.
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
