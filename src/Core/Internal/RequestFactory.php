<?php

declare(strict_types=1);

namespace Retrofit\Core\Internal;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;
use Retrofit\Core\Attribute\HttpRequest;
use Retrofit\Core\Internal\ParameterHandler\ParameterHandler;

readonly class RequestFactory
{
    /**
     * @param array<string, string> $defaultHeaders
     * @param ParameterHandler[] $parameterHandlers
     */
    public function __construct(
        private UriInterface $baseUrl,
        private HttpRequest $httpRequest,
        private array $defaultHeaders,
        private array $parameterHandlers,
    )
    {
    }

    /**
     * @param list<mixed> $args
     * @return RequestInterface
     */
    public function create(array $args): RequestInterface
    {
        $requestBuilder = new RequestBuilder($this->baseUrl, $this->httpRequest, $this->defaultHeaders);

        foreach ($this->parameterHandlers as $i => $parameterHandler) {
            $parameterHandler->apply($requestBuilder, $args[$i]);
        }

        return $requestBuilder->build();
    }
}
