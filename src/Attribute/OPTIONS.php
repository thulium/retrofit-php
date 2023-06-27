<?php
declare(strict_types=1);

namespace Retrofit\Attribute;

use Attribute;
use Retrofit\HttpMethod;
use Retrofit\Internal\Utils\Utils;

#[Attribute(Attribute::TARGET_METHOD)]
readonly class OPTIONS implements HttpRequest
{
    private array $pathParameters;

    public function __construct(private string $path)
    {
        $this->pathParameters = Utils::parsePathParameters($this->path);
    }

    public function httpMethod(): HttpMethod
    {
        return HttpMethod::OPTIONS;
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
        return false;
    }
}
