<?php
declare(strict_types=1);

namespace Retrofit\Attribute;

use Attribute;
use Retrofit\HttpMethod;
use Retrofit\Internal\Utils\Utils;

#[Attribute(Attribute::TARGET_METHOD)]
readonly class HTTP implements HttpRequest
{
    private HttpMethod $httpMethod;
    private array $pathParameters;

    public function __construct(
        HttpMethod|string $httpMethod,
        private string $path = '',
        private bool $hasBody = false
    )
    {
        if (is_string($httpMethod)) {
            $httpMethod = HttpMethod::from($httpMethod);
        }
        $this->httpMethod = $httpMethod;
        $this->pathParameters = Utils::parsePathParameters($this->path);
    }

    public function httpMethod(): HttpMethod
    {
        return $this->httpMethod;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function pathParameters(): array
    {
        return $this->pathParameters;
    }

    public function hasBody(): bool
    {
        return $this->hasBody;
    }
}
