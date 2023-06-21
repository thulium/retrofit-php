<?php
declare(strict_types=1);

namespace Retrofit;

use LogicException;
use Psr\Http\Message\UriInterface;

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

    public function baseUrl(UriInterface $baseUUrl): static
    {
        $this->baseUrl = $baseUUrl;
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
            throw new LogicException('Base URL must be provided');
        }

        if (is_null($this->httpClient)) {
            throw new LogicException('Must set http client to make requests');
        }

        return new Retrofit();
    }
}
