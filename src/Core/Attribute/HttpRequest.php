<?php

declare(strict_types=1);

namespace Retrofit\Core\Attribute;

use Retrofit\Core\HttpMethod;

interface HttpRequest
{
    public function httpMethod(): HttpMethod;

    public function path(): ?string;

    /**
     * @return list<string>
     */
    public function pathParameters(): array;

    public function hasBody(): bool;
}
