<?php

namespace Retrofit\Internal;

use Nyholm\Psr7\Request;
use Nyholm\Psr7\Uri;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;
use Retrofit\Attribute\HttpRequest;

class RequestBuilder
{
    private UriInterface $uri;

    public function __construct(
        UriInterface $baseUrl,
        private readonly HttpRequest $httpRequest
    )
    {
        $this->uri = new Uri($baseUrl->__toString() . $httpRequest->path());
    }

    public function addPathParam(string $name, string $value, bool $encoded): void
    {
        if ($encoded) {
            $value = rawurlencode($value);
        }
        $path = rawurldecode($this->uri->getPath());
        $path = str_replace(sprintf('{%s}', $name), $value, $path);
        $this->uri = $this->uri->withPath($path);
    }

    public function build(): RequestInterface
    {
        return new Request($this->httpRequest->httpMethod()->value, $this->uri);
    }
}
