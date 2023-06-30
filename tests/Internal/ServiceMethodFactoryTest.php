<?php
declare(strict_types=1);

namespace Retrofit\Tests\Internal;

use Nyholm\Psr7\Uri;
use Ouzo\Tests\Mock\Mock;
use Ouzo\Tests\Mock\MockInterface;
use PhpParser\BuilderFactory;
use PhpParser\PrettyPrinter\Standard;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Retrofit\HttpClient;
use Retrofit\Internal\BuiltInConverterFactory;
use Retrofit\Internal\ConverterProvider;
use Retrofit\Internal\Proxy\DefaultProxyFactory;
use Retrofit\Internal\ServiceMethodFactory;
use Retrofit\Retrofit;
use Retrofit\Tests\Fixtures\Api\InvalidMethods;
use RuntimeException;

class ServiceMethodFactoryTest extends TestCase
{
    #[Test]
    public function shouldThrowExceptionWhenMethodDoesNotHaveHttpAttribute(): void
    {
        //given
        /** @var HttpClient|MockInterface $httpClient */
        $httpClient = Mock::create(HttpClient::class);
        $baseUrl = new Uri('https://example.com');
        $converterProvider = new ConverterProvider([new BuiltInConverterFactory()]);
        $proxyFactory = new DefaultProxyFactory(new BuilderFactory(), new Standard());

        $retrofit = new Retrofit($httpClient, $baseUrl, $converterProvider, $proxyFactory);

        //when
        try {
            ServiceMethodFactory::create($retrofit, InvalidMethods::class, 'withoutHttpAttribute');
        } catch (RuntimeException $e) {
            //then
            $this->assertSame('Method InvalidMethods::withoutHttpAttribute(). HTTP method annotation is required (e.g., #[GET], #[POST], etc.).', $e->getMessage());
        }
    }

    #[Test]
    public function shouldThrowExceptionWhenMethodHasMultipleHttpAttributes(): void
    {
        //given
        /** @var HttpClient|MockInterface $httpClient */
        $httpClient = Mock::create(HttpClient::class);
        $baseUrl = new Uri('https://example.com');
        $converterProvider = new ConverterProvider([new BuiltInConverterFactory()]);
        $proxyFactory = new DefaultProxyFactory(new BuilderFactory(), new Standard());

        $retrofit = new Retrofit($httpClient, $baseUrl, $converterProvider, $proxyFactory);

        //when
        try {
            ServiceMethodFactory::create($retrofit, InvalidMethods::class, 'multipleHttpAttribute');
        } catch (RuntimeException $e) {
            //then
            $this->assertSame('Method InvalidMethods::multipleHttpAttribute(). Only one HTTP method is allowed. Found: [Retrofit\Attribute\GET, Retrofit\Attribute\HTTP].', $e->getMessage());
        }
    }

    #[Test]
    public function shouldThrowExceptionWhenMultipleUrlAttributesFound(): void
    {
        //given
        /** @var HttpClient|MockInterface $httpClient */
        $httpClient = Mock::create(HttpClient::class);
        $baseUrl = new Uri('https://example.com');
        $converterProvider = new ConverterProvider([new BuiltInConverterFactory()]);
        $proxyFactory = new DefaultProxyFactory(new BuilderFactory(), new Standard());

        $retrofit = new Retrofit($httpClient, $baseUrl, $converterProvider, $proxyFactory);

        //when
        try {
            ServiceMethodFactory::create($retrofit, InvalidMethods::class, 'multipleUrlAttributes');
        } catch (RuntimeException $e) {
            //then
            $this->assertSame('Method InvalidMethods::multipleUrlAttributes() parameter #2. Multiple #[Url] method attributes found.', $e->getMessage());
        }
    }
}
