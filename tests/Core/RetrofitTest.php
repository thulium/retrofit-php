<?php

declare(strict_types=1);

namespace Retrofit\Tests\Core;

use GuzzleHttp\Psr7\Uri;
use InvalidArgumentException;
use Ouzo\Tests\CatchException;
use Ouzo\Tests\Mock\Mock;
use Ouzo\Tests\Mock\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Retrofit\Core\HttpClient;
use Retrofit\Core\Internal\ConverterProvider;
use Retrofit\Core\Internal\Proxy\ProxyFactory;
use Retrofit\Core\Retrofit;
use Retrofit\Core\RetrofitBuilder;
use Retrofit\Tests\Fixtures\Api\FullyValidApi;
use Retrofit\Tests\Fixtures\Api\NotInterface;
use stdClass;

class RetrofitTest extends TestCase
{
    #[Test]
    public function shouldGetBuilder(): void
    {
        // when
        $retrofitBuilder = Retrofit::Builder();

        // then
        $this->assertInstanceOf(RetrofitBuilder::class, $retrofitBuilder);
    }

    #[Test]
    public function shouldThrowExceptionWhenServiceIsNotAnInterface(): void
    {
        // given
        /** @var HttpClient|MockInterface $httpClient */
        $httpClient = Mock::create(HttpClient::class);
        $baseUrl = new Uri('https://example.com');
        $converterProvider = new ConverterProvider([]);
        /** @var ProxyFactory|MockInterface $proxyFactory */
        $proxyFactory = Mock::create(ProxyFactory::class);

        $retrofit = new Retrofit($httpClient, $baseUrl, $converterProvider, $proxyFactory);

        // when
        CatchException::when($retrofit)->create(NotInterface::class);

        // then
        CatchException::assertThat()
            ->isInstanceOf(InvalidArgumentException::class)
            ->hasMessage("Service 'NotInterface' API declarations must be interface.");
    }

    #[Test]
    public function shouldCreateImplementationOfService(): void
    {
        // given
        /** @var HttpClient|MockInterface $httpClient */
        $httpClient = Mock::create(HttpClient::class);
        $baseUrl = new Uri('https://example.com');
        $converterProvider = new ConverterProvider([]);
        /** @var ProxyFactory|MockInterface $proxyFactory */
        $proxyFactory = Mock::create(ProxyFactory::class);

        Mock::when($proxyFactory)->create(Mock::anyArgList())->thenReturn(new stdClass());

        $retrofit = new Retrofit($httpClient, $baseUrl, $converterProvider, $proxyFactory);

        // when
        $impl = $retrofit->create(FullyValidApi::class);

        // then
        $this->assertInstanceOf(stdClass::class, $impl);

        Mock::verify($proxyFactory)->create(Mock::argThat()->isInstanceOf(Retrofit::class), new ReflectionClass(FullyValidApi::class));
    }
}
