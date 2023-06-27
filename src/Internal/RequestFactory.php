<?php
declare(strict_types=1);

namespace Retrofit\Internal;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;
use Retrofit\Attribute\HttpRequest;
use Retrofit\Internal\ParameterHandler\ParameterHandler;

readonly class RequestFactory
{
    /**
     * @param ParameterHandler[] $parameterHandlers
     */
    public function __construct(
        private UriInterface $baseUrl,
        private HttpRequest $httpRequest,
        private array $parameterHandlers
    )
    {
    }

    public function create(array $args): RequestInterface
    {
        $requestBuilder = new RequestBuilder($this->baseUrl, $this->httpRequest);

        foreach ($this->parameterHandlers as $i => $parameterHandler) {
            $parameterHandler->apply($requestBuilder, $args[$i]);
        }

        return $requestBuilder->build();
    }
}
