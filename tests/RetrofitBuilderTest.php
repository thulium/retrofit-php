<?php
declare(strict_types=1);

namespace Retrofit\Tests;

use GuzzleHttp\Psr7\Uri;
use LogicException;
use Ouzo\Tests\CatchException;
use Ouzo\Tests\Mock\Mock;
use Ouzo\Tests\Mock\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Retrofit\Converter\Converter;
use Retrofit\Converter\ResponseBodyConverter;
use Retrofit\Converter\StringConverter;
use Retrofit\HttpClient;
use Retrofit\RetrofitBuilder;
use Retrofit\Tests\Fixtures\Converter\TestConverterFactory;
use Retrofit\Type;
use stdClass;

class RetrofitBuilderTest extends TestCase
{
    #[Test]
    public function shouldThrowExceptionWhenHttpClientIsNotSet(): void
    {
        //given
        $retrofitBuilder = new RetrofitBuilder();

        //when
        CatchException::when($retrofitBuilder)->build();

        //then
        CatchException::assertThat()
            ->isInstanceOf(LogicException::class)
            ->hasMessage('Must set HttpClient object to make requests.');
    }

    #[Test]
    public function shouldThrowExceptionWhenBaseUrlIsNotSet(): void
    {
        //given
        /** @var HttpClient|MockInterface $httpClient */
        $httpClient = Mock::create(HttpClient::class);
        $retrofitBuilder = (new RetrofitBuilder())->client($httpClient);

        //when
        CatchException::when($retrofitBuilder)->build();

        //then
        CatchException::assertThat()
            ->isInstanceOf(LogicException::class)
            ->hasMessage('Base URL required.');
    }

    #[Test]
    public function shouldBuildRetrofit(): void
    {
        //given
        /** @var HttpClient|MockInterface $httpClient */
        $httpClient = Mock::create(HttpClient::class);
        $retrofitBuilder = (new RetrofitBuilder())
            ->client($httpClient)
            ->baseUrl('https://example.com');

        //when
        $retrofit = $retrofitBuilder->build();

        //then
        $this->assertSame($httpClient, $retrofit->httpClient);
        $this->assertEquals(new Uri('https://example.com'), $retrofit->baseUrl);
    }

    #[Test]
    public function shouldBuildRetrofitUsingUriInterfaceAsBaseUrl(): void
    {
        //given
        /** @var HttpClient|MockInterface $httpClient */
        $httpClient = Mock::create(HttpClient::class);
        $baseUrl = new Uri('https://example.com');
        $retrofitBuilder = (new RetrofitBuilder())
            ->client($httpClient)
            ->baseUrl($baseUrl);

        //when
        $retrofit = $retrofitBuilder->build();

        //then
        $this->assertSame($httpClient, $retrofit->httpClient);
        $this->assertSame($baseUrl, $retrofit->baseUrl);
    }

    #[Test]
    public function shouldBuildRetrofitWithBuiltInConverters(): void
    {
        //given
        /** @var HttpClient|MockInterface $httpClient */
        $httpClient = Mock::create(HttpClient::class);
        $baseUrl = new Uri('https://example.com');
        $retrofitBuilder = (new RetrofitBuilder())
            ->client($httpClient)
            ->baseUrl($baseUrl);

        //when
        $retrofit = $retrofitBuilder->build();

        //then
        $converterProvider = $retrofit->converterProvider;
        $converter = $converterProvider->getStringConverter(new Type('string'));
        $this->assertInstanceOf(Converter::class, $converter);
        $this->assertInstanceOf(StringConverter::class, $converter);
        $this->assertStringStartsWith('Retrofit\Converter\StringConverter@anonymous', $converter::class);
        $this->assertStringContainsString('src/Internal/BuiltInConverters.php', $converter::class);
    }

    #[Test]
    public function shouldBuildRetrofitWithAdditionalConverters(): void
    {
        //given
        /** @var HttpClient|MockInterface $httpClient */
        $httpClient = Mock::create(HttpClient::class);
        $baseUrl = new Uri('https://example.com');
        $retrofitBuilder = (new RetrofitBuilder())
            ->client($httpClient)
            ->baseUrl($baseUrl)
            ->addConverterFactory(new TestConverterFactory());

        //when
        $retrofit = $retrofitBuilder->build();

        //then
        $converterProvider = $retrofit->converterProvider;
        $converter = $converterProvider->getResponseBodyConverter(new Type(stdClass::class));
        $this->assertInstanceOf(Converter::class, $converter);
        $this->assertInstanceOf(ResponseBodyConverter::class, $converter);
        $this->assertStringStartsWith('Retrofit\Converter\ResponseBodyConverter@anonymous', $converter::class);
        $this->assertStringContainsString('src/Internal/BuiltInConverters.php', $converter::class);
    }
}
