<?php
declare(strict_types=1);

namespace Retrofit\Tests\Internal;

use Nyholm\Psr7\Uri;
use Ouzo\Tests\CatchException;
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
    private ServiceMethodFactory $serviceMethodFactory;

    public function setUp(): void
    {
        parent::setUp();
        /** @var HttpClient|MockInterface $httpClient */
        $httpClient = Mock::create(HttpClient::class);
        $baseUrl = new Uri('https://example.com');
        $converterProvider = new ConverterProvider([new BuiltInConverterFactory()]);
        $proxyFactory = new DefaultProxyFactory(new BuilderFactory(), new Standard());

        $retrofit = new Retrofit($httpClient, $baseUrl, $converterProvider, $proxyFactory);

        $this->serviceMethodFactory = new ServiceMethodFactory($retrofit);
    }

    #[Test]
    public function shouldThrowExceptionWhenMethodDoesNotHaveHttpAttribute(): void
    {
        //when
        CatchException::when($this->serviceMethodFactory)->create(InvalidMethods::class, 'withoutHttpAttribute');

        //then
        CatchException::assertThat()
            ->isInstanceOf(RuntimeException::class)
            ->hasMessage('Method InvalidMethods::withoutHttpAttribute(). HTTP method annotation is required (e.g., #[GET], #[POST], etc.).');
    }

    #[Test]
    public function shouldThrowExceptionWhenMethodHasMultipleHttpAttributes(): void
    {
        //when
        CatchException::when($this->serviceMethodFactory)->create(InvalidMethods::class, 'multipleHttpAttribute');

        //then
        CatchException::assertThat()
            ->isInstanceOf(RuntimeException::class)
            ->hasMessage('Method InvalidMethods::multipleHttpAttribute(). Only one HTTP method is allowed. Found: [Retrofit\Attribute\GET, Retrofit\Attribute\HTTP].');
    }

    #[Test]
    public function shouldThrowExceptionWhenMultipleUrlAttributesFound(): void
    {
        //when
        CatchException::when($this->serviceMethodFactory)->create(InvalidMethods::class, 'multipleUrlAttributes');

        //then
        CatchException::assertThat()
            ->isInstanceOf(RuntimeException::class)
            ->hasMessage('Method InvalidMethods::multipleUrlAttributes() parameter #2. Multiple #[Url] method attributes found.');
    }

    #[Test]
    public function shouldThrowExceptionWhenPathAndUrlAttributesAreSetTogether(): void
    {
        //when
        CatchException::when($this->serviceMethodFactory)->create(InvalidMethods::class, 'urlAndPathSetTogether');

        //then
        CatchException::assertThat()
            ->isInstanceOf(RuntimeException::class)
            ->hasMessage('Method InvalidMethods::urlAndPathSetTogether() parameter #2. #[Path] parameters may not be used with #[Url].');
    }
}
