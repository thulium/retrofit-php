<?php
declare(strict_types=1);

namespace Retrofit;

use LogicException;
use PhpParser\BuilderFactory;
use PhpParser\PrettyPrinter\Standard;
use Retrofit\Proxy\DefaultProxyFactory;

/**
 * Build a new {@link Retrofit}.
 */
class RetrofitBuilder
{
    private ?HttpClient $httpClient = null;
    private ?string $baseUrl = null;
    private array $converterFactories = [];

    public function client(HttpClient $httpClient): static
    {
        $this->httpClient = $httpClient;
        return $this;
    }

    public function baseUrl(string $baseUrl): static
    {
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
        if (is_null($this->baseUrl)) {
            throw new LogicException('Base URL required');
        }

        if (is_null($this->httpClient)) {
            throw new LogicException('Must set HttpClient object to make requests');
        }

        $proxyFactory = new DefaultProxyFactory(new BuilderFactory(), new Standard());

        return new Retrofit($this->httpClient, $proxyFactory);
    }
}
