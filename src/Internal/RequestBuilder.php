<?php
declare(strict_types=1);

namespace Retrofit\Internal;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use JetBrains\PhpStorm\ArrayShape;
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

    public function setBaseUrl(UriInterface|string $value): void
    {
        if (is_string($value)) {
            $value = new Uri($value);
        }
        $this->uri = $value->withQuery($this->uri->getQuery());
    }

    public function addPathParam(string $name, string $value, bool $encoded): void
    {
        if ($encoded) {
            $value = rawurlencode($value);
        }
        $this->pathParameters[] = ['name' => $name, 'value' => $value];
    }

    public function addQueryParam(string $name, string|array $value, bool $encoded): void
    {
        if (is_array($value)) {
            foreach ($value as $v) {
                if ($encoded) {
                    $v = rawurlencode($v);
                }

                $currentQuery = $this->uri->getQuery();
                $newQueryPart = "{$name}={$v}";
                $query = Strings::isNotBlank($currentQuery) ? implode('&', [$currentQuery, $newQueryPart]) : $newQueryPart;
                $this->uri = $this->uri->withQuery($query);
            }
        } else {
            if ($encoded) {
                $value = rawurlencode($value);
            }
            $this->uri = Uri::withQueryValue($this->uri, $name, $value);
        }
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
}
