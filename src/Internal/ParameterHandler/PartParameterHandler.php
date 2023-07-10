<?php
declare(strict_types=1);

namespace Retrofit\Internal\ParameterHandler;

use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use ReflectionMethod;
use Retrofit\Converter;
use Retrofit\Internal\RequestBuilder;
use Retrofit\Internal\Utils\Utils;
use Retrofit\MimeEncoding;
use Retrofit\Multipart\PartInterface;

readonly class PartParameterHandler implements ParameterHandler
{
    private const CONTENT_TRANSFER_ENCODING_HEADER = 'Content-Transfer-Encoding';

    public function __construct(
        private ?string $name,
        private MimeEncoding $encoding,
        private Converter $converter,
        private ReflectionMethod $reflectionMethod,
        private int $position
    )
    {
    }

    public function apply(RequestBuilder $requestBuilder, mixed $value): void
    {
        if (is_null($value)) {
            return;
        }

        if (Strings::isBlank($this->name) && !$value instanceof PartInterface) {
            throw Utils::parameterException($this->reflectionMethod, $this->position,
                '#[Part] attribute must supply a name or use MultipartBody.Part parameter type.');
        }

        if (Strings::isNotBlank($this->name) && $value instanceof PartInterface) {
            throw Utils::parameterException($this->reflectionMethod, $this->position,
                '#[Part] attribute using the MultipartBody.Part must not include a part name in the attribute.');
        }

        if ($value instanceof PartInterface) {
            $headers = $value->getHeaders();
            $headerNames = Arrays::mapKeys($headers, fn(string $key): string => strtolower($key));
            if (!in_array(strtolower(self::CONTENT_TRANSFER_ENCODING_HEADER), $headerNames)) {
                $headers[self::CONTENT_TRANSFER_ENCODING_HEADER] = $this->encoding->value;
            }
            $requestBuilder->addPart($value->getName(), $value->getBody(), $headers, $value->getFilename());
            return;
        }

        $value = $this->converter->convert($value);
        $headers[self::CONTENT_TRANSFER_ENCODING_HEADER] = $this->encoding->value;
        $requestBuilder->addPart($this->name, $value, $headers);
    }
}
