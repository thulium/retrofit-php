<?php

declare(strict_types=1);

namespace Retrofit\Core\Attribute;

use Attribute;
use Retrofit\Core\HttpMethod;
use Retrofit\Core\Internal\Utils\Utils;

/**
 * Make a PUT request.
 *
 * @api
 */
#[Attribute(Attribute::TARGET_METHOD)]
readonly class PUT implements HttpRequest
{
    /** @var list<string> */
    private array $pathParameters;

    public function __construct(private ?string $path = null)
    {
        $this->pathParameters = Utils::parsePathParameters($this->path);
    }

    public function httpMethod(): HttpMethod
    {
        return HttpMethod::PUT;
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
        return true;
    }
}
