<?php

namespace Retrofit\Attribute;

use Retrofit\HttpMethod;

interface HttpRequest
{
    public function httpMethod(): HttpMethod;

    public function path(): string;

    public function hasBody(): bool;
}