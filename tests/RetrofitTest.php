<?php

namespace Retrofit\Tests;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Retrofit\HttpClient;
use Retrofit\HttpMethod;
use Retrofit\Retrofit;
use Retrofit\Tests\Fixtures\SomeApi;

class RetrofitTest extends TestCase
{
    #[Test]
    public function shouldName(): void
    {
        $form = HttpMethod::from('GET');

        $retrofit = Retrofit::builder()
            ->client($this->httClient())
            ->baseUrl('https://example.com')
            ->build();

        /** @var SomeApi $someApi */
        $someApi = $retrofit->create(SomeApi::class);
        $call = $someApi->getUserByName('Jon+Doe');

        $this->assertTrue(true);
    }

    public function httClient(): HttpClient
    {
        return new class implements HttpClient {
            public function send(RequestInterface $request): ResponseInterface
            {
                return new \stdClass();
            }

            public function sendAsync(RequestInterface $request, callable $onResponse, callable $onFailure): void
            {
            }

            public function wait(): void
            {
            }
        };
    }
}
