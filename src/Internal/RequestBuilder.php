<?php
declare(strict_types=1);

namespace Retrofit\Internal;

use Nyholm\Psr7\Request;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;
use Retrofit\Attribute\HttpRequest;
use Retrofit\Internal\Handler\ParameterHandler;

class RequestBuilder
{
    private UriInterface $baseUrl;
    private HttpRequest $httpRequest;
    /** @var ParameterHandler[] */
    private array $parameterHandlers;
    private array $args;

    public function baseUrl(UriInterface $baseUrl): static
    {
        $this->baseUrl = $baseUrl;
        return $this;
    }

    public function httpRequest(HttpRequest $httpRequest): static
    {
        $this->httpRequest = $httpRequest;
        return $this;
    }

    public function parameterHandlers(array $parameterHandlers): static
    {
        $this->parameterHandlers = $parameterHandlers;
        return $this;
    }

    public function withArgs(array $args): static
    {
        $this->args = $args;
        return $this;
    }

    public function build(): RequestInterface
    {

        foreach ($this->parameterHandlers as $i => $parameterHandler) {
        }

        return new Request($this->httpRequest->httpMethod()->value, null);
    }
}
