<?php

declare(strict_types=1);

namespace Retrofit\Core;

use GuzzleHttp\Psr7\Uri;
use LogicException;
use PhpParser\BuilderFactory;
use PhpParser\PrettyPrinter\Standard;
use Psr\Http\Message\UriInterface;
use Retrofit\Core\Converter\ConverterFactory;
use Retrofit\Core\Internal\BuiltInConverterFactory;
use Retrofit\Core\Internal\ConverterProvider;
use Retrofit\Core\Internal\Proxy\DefaultProxyFactory;

/**
 * Build a new {@link Retrofit}.
 */
class RetrofitBuilder
{
    private ?HttpClient $httpClient = null;
    private ?UriInterface $baseUrl = null;
    private array $converterFactories = [];

    public function client(HttpClient $httpClient): static
    {
        $this->httpClient = $httpClient;
        return $this;
    }

    public function baseUrl(UriInterface|string $baseUrl): static
    {
        if (is_string($baseUrl)) {
            $baseUrl = new Uri($baseUrl);
        }
        $this->baseUrl = $baseUrl;
        return $this;
    }

    public function addConverterFactory(ConverterFactory $converterFactory): static
    {
        $this->converterFactories[] = $converterFactory;
        return $this;
    }

    public function build(): Retrofit
    {
        if (is_null($this->httpClient)) {
            throw new LogicException('Must set HttpClient object to make requests.');
        }

        if (is_null($this->baseUrl)) {
            throw new LogicException('Base URL required.');
        }

        $this->converterFactories[] = new BuiltInConverterFactory();

        $proxyFactory = new DefaultProxyFactory(new BuilderFactory(), new Standard());

        $converterProvider = new ConverterProvider($this->converterFactories);
        return new Retrofit($this->httpClient, $this->baseUrl, $converterProvider, $proxyFactory);
    }
}
