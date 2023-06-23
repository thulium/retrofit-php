<?php

namespace Retrofit\Tests\Proxy;

use PhpParser\BuilderFactory;
use PhpParser\PrettyPrinter\Standard;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use ReflectionClass;
use Retrofit\HttpClient;
use Retrofit\Proxy\DefaultProxyFactory;
use Retrofit\Retrofit;
use Retrofit\Tests\Fixtures\SomeApi;

class DefaultProxyFactoryTest extends TestCase
{
    #[Test]
    public function shouldCreate(): void
    {
        //given
        $httpClient = new class implements HttpClient {
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

        $defaultProxyFactory = new DefaultProxyFactory(new BuilderFactory(), new Standard());
        $retrofit = new Retrofit($httpClient, $defaultProxyFactory);

        //when
        $stdClass = $defaultProxyFactory->create($retrofit, new ReflectionClass(SomeApi::class));

        $users = $stdClass->getUsers()->execute();

        //then
        $this->assertEquals('', $users);
    }
}
