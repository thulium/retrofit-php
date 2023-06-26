<?php
declare(strict_types=1);

namespace Retrofit\Attribute;

use Attribute;
use Retrofit\HttpMethod;

#[Attribute(Attribute::TARGET_METHOD)]
readonly class DELETE implements HttpRequest
{
    public function __construct(private string $path)
    {
    }

    public function httpMethod(): HttpMethod
    {
        return HttpMethod::DELETE;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function hasBody(): bool
    {
        return false;
    }
}
