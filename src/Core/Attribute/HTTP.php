<?php

declare(strict_types=1);

namespace Retrofit\Core\Attribute;

use Attribute;
use Retrofit\Core\HttpMethod;
use Retrofit\Core\Internal\Utils\Utils;

#[Attribute(Attribute::TARGET_METHOD)]
readonly class HTTP implements HttpRequest
{
    private HttpMethod $httpMethod;
    private array $pathParameters;

    public function __construct(
        HttpMethod|string $httpMethod,
        private ?string $path = null,
        private bool $hasBody = false,
    ) {
        $this->httpMethod = is_string($httpMethod) ? HttpMethod::from($httpMethod) : $httpMethod;
        $this->pathParameters = Utils::parsePathParameters($this->path);
    }

    public function httpMethod(): HttpMethod
    {
        return $this->httpMethod;
    }

    public function path(): ?string
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
