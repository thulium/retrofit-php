<?php
declare(strict_types=1);

namespace Retrofit\Internal;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use JetBrains\PhpStorm\ArrayShape;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Strings;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;
use Retrofit\Attribute\HttpRequest;
use Retrofit\Internal\Utils\Utils;
use RuntimeException;

class RequestBuilder
{
    private const PARAMETER_PLACEHOLDER = '{%s}';

    private UriInterface $uri;
    #[ArrayShape([0 => ['name' => 'string', 'value' => 'string']])]
    private array $pathParameters = [];
    private array $headers = [];

    /**
     * @param array<string, string> $defaultHeaders
     */
    public function __construct(
        UriInterface $baseUrl,
        private readonly HttpRequest $httpRequest,
        array $defaultHeaders = []
    )
    {
        $this->uri = new Uri($baseUrl->__toString());
        if (!is_null($this->httpRequest->path())) {
            $this->uri = $this->uri->withPath($this->httpRequest->path());
        }

        collect($defaultHeaders)->each(function (string $value, string $name): void {
            $this->addHeader($name, $value);
        });
    }

    public function setBaseUrl(UriInterface|string $value): void
    {
        if (is_string($value)) {
            $value = new Uri($value);
        }
        $this->uri = $value->withQuery($this->uri->getQuery());
    }

    public function addPathParam(string $name, string $value, bool $encoded): void
    {
        if (!$encoded) {
            $value = rawurlencode($value);
        }
        $this->pathParameters[] = ['name' => $name, 'value' => $value];
    }

    private array $queries = [];

    public function addQueryParam(string|array $name, string|array|null $value, bool $encoded): void
    {
        if (is_null($value)) {
            $name = Arrays::toArray($name);
            foreach ($name as $item) {
                if (!$encoded) {
                    $item = rawurlencode($item);
                }
                $this->queries[] = $item;
            }
        } else {
            $value = Arrays::toArray($value);
            foreach ($value as $item) {
                if (!$encoded) {
                    $item = rawurlencode($item);
                }
                $this->queries[] = "{$name}={$item}";
            }
        }
    }

    public function addHeader(string $name, string $value): void
    {
        $name = strtolower($name);
        $this->headers[$name] = $value;
    }

    public function build(): RequestInterface
    {
        $this->replacePathParameters();
        $this->initializeQueryString();

        return new Request($this->httpRequest->httpMethod()->value, $this->uri, $this->headers);
    }

    private function replacePathParameters(): void
    {
        if (!empty($this->pathParameters)) {
            $path = rawurldecode($this->uri->getPath());
            $parsePathParameters = Utils::parsePathParameters($path);
            foreach ($this->pathParameters as $pathParameter) {
                $name = $pathParameter['name'];
                if (!in_array($name, $parsePathParameters)) {
                    throw new RuntimeException("URL '{$path}' does not contain '{$name}'.");
                }

                $path = str_replace(sprintf(self::PARAMETER_PLACEHOLDER, $pathParameter['name']), $pathParameter['value'], $path);
            }
            $this->uri = $this->uri->withPath($path);
        }
    }

    private function initializeQueryString(): void
    {
        if (!empty($this->queries)) {
            $query = implode('&', $this->queries);
            $this->uri = Strings::isBlank($this->uri->getQuery()) ? $this->uri->withQuery($query) : $this->uri->withQuery("{$query}&{$this->uri->getQuery()}");
        }
    }
}
