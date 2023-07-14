<?php
declare(strict_types=1);

namespace Retrofit\Internal;

use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\StreamInterface;
use Retrofit\Converter\RequestBodyConverter;
use Retrofit\Converter\ResponseBodyConverter;
use Retrofit\Converter\StringConverter;
use Retrofit\Type;
use stdClass;

readonly class BuiltInConverters
{
    public static function JsonEncodeRequestBodyConverter(): RequestBodyConverter
    {
        return new class implements RequestBodyConverter {
            public function convert(mixed $value): StreamInterface
            {
                return Utils::streamFor(json_encode($value));
            }
        };
    }

    public static function StreamInterfaceRequestBodyConverter(): RequestBodyConverter
    {
        return new class implements RequestBodyConverter {
            public function convert(mixed $value): StreamInterface
            {
                return $value;
            }
        };
    }

    public static function StreamInterfaceResponseBodyConverter(): ResponseBodyConverter
    {
        return new class implements ResponseBodyConverter {
            public function convert(StreamInterface $value): StreamInterface
            {
                return $value;
            }
        };
    }

    public static function StdClassResponseBodyConverter(): ResponseBodyConverter
    {
        return new class implements ResponseBodyConverter {
            public function convert(StreamInterface $value): stdClass
            {
                return json_decode($value->getContents());
            }
        };
    }

    public static function ArrayResponseBodyConverter(Type $type): ResponseBodyConverter
    {
        return new class($type) implements ResponseBodyConverter {
            public function __construct(private readonly Type $type)
            {
            }

            public function convert(StreamInterface $value): array
            {
                return json_decode($value->getContents(), $this->type->parametrizedTypeIsScalar());
            }
        };
    }

    public static function VoidResponseBodyConverter(): ResponseBodyConverter
    {
        return new class implements ResponseBodyConverter {
            public function convert(StreamInterface $value): null
            {
                return null;
            }
        };
    }

    public static function ScalarTypeResponseBodyConverter(Type $type): ResponseBodyConverter
    {
        return new class($type) implements ResponseBodyConverter {
            public function __construct(private readonly Type $type)
            {
            }

            public function convert(StreamInterface $value): mixed
            {
                $contents = $value->getContents();

                return match ($this->type->getRawType()) {
                    'bool' => (bool)$contents,
                    'float' => (float)$contents,
                    'int' => (int)$contents,
                    'string' => (string)$contents,
                    default => null
                };
            }
        };
    }

    public static function ToStringConverter(): StringConverter
    {
        return new class implements StringConverter {
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
