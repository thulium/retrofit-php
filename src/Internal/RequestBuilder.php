<?php
declare(strict_types=1);

namespace Retrofit\Internal;

use JetBrains\PhpStorm\ArrayShape;
use Nyholm\Psr7\Request;
use Nyholm\Psr7\Uri;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;
use Retrofit\Attribute\HttpRequest;

class RequestBuilder
{
    private const PARAMETER_PLACEHOLDER = '{%s}';

    private UriInterface $uri;
    #[ArrayShape([0 => ['name' => 'string', 'value' => 'string']])]
    private array $pathParameters = [];

    public function __construct(
        UriInterface $baseUrl,
        private readonly HttpRequest $httpRequest
    )
    {
        $this->uri = new Uri($baseUrl->__toString());
        if (!is_null($this->httpRequest->path())) {
            $this->uri = $this->uri->withPath($this->httpRequest->path());
        }
    }

    public function setBaseUrl(Uri|string $value): void
    {
        if (is_string($value)) {
            $value = new Uri($value);
        }
        $this->uri = $value;
    }

    public function addPathParam(string $name, string $value, bool $encoded): void
    {
        if ($encoded) {
            $value = rawurlencode($value);
        }
        $this->pathParameters[] = ['name' => $name, 'value' => $value];
    }

    public function build(): RequestInterface
    {
        $this->replacePathParameters();

        return new Request($this->httpRequest->httpMethod()->value, $this->uri);
    }

    private function replacePathParameters(): void
    {
        if (!empty($this->pathParameters)) {
            $path = rawurldecode($this->uri->getPath());
            foreach ($this->pathParameters as $pathParameter) {
                $path = str_replace(sprintf(self::PARAMETER_PLACEHOLDER, $pathParameter['name']), $pathParameter['value'], $path);
            }
            $this->uri = $this->uri->withPath($path);
        }
    }
}
