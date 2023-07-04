<?php
declare(strict_types=1);

namespace Retrofit\Tests\Internal;

use GuzzleHttp\Psr7\Uri;
use Ouzo\Tests\Assert;
use Ouzo\Tests\CatchException;
use Ouzo\Tests\Mock\Mock;
use Ouzo\Tests\Mock\MockInterface;
use PhpParser\BuilderFactory;
use PhpParser\PrettyPrinter\Standard;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use Retrofit\HttpClient;
use Retrofit\Internal\BuiltInConverterFactory;
use Retrofit\Internal\ConverterProvider;
use Retrofit\Internal\Proxy\DefaultProxyFactory;
use Retrofit\Internal\ServiceMethodFactory;
use Retrofit\Retrofit;
use Retrofit\Tests\Fixtures\Api\AllHttpRequestMethods;
use Retrofit\Tests\Fixtures\Api\FullyValidApi;
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
    #[TestWith(['delete'])]
    #[TestWith(['get'])]
    #[TestWith(['head'])]
    #[TestWith(['options'])]
    #[TestWith(['patch'])]
    #[TestWith(['post'])]
    #[TestWith(['put'])]
    public function shouldInvokeAllHttpMethods(string $method): void
    {
        //when
        $serviceMethod = $this->serviceMethodFactory->create(AllHttpRequestMethods::class, $method);

        //then
        $request = $serviceMethod->invoke([])->request();
        $this->assertSame(strtoupper($method), $request->getMethod());
        $this->assertSame("https://example.com/{$method}", $request->getUri()->__toString());
    }

    #[Test]
    public function shouldProcessUrlBeforePath(): void
    {
        //when
        $serviceMethod = $this->serviceMethodFactory->create(FullyValidApi::class, 'pathIsBeforeUrl');

        //then
        $request = $serviceMethod->invoke(['jon', 'https://example.com/users/{login}'])->request();
        $this->assertSame('https://example.com/users/jon', $request->getUri()->__toString());
    }

    #[Test]
    public function shouldAddQueryStringToUrlAttribute(): void
    {
        //when
        $serviceMethod = $this->serviceMethodFactory->create(FullyValidApi::class, 'urlWithQuery');

        //then
        $request = $serviceMethod->invoke(['new', 'https://example.com/users'])->request();
        $this->assertSame('https://example.com/users?group=new', $request->getUri()->__toString());
    }

    #[Test]
    public function shouldAddPathAndQueryString(): void
    {
        //when
        $serviceMethod = $this->serviceMethodFactory->create(FullyValidApi::class, 'pathAndQuery');

        //then
        $request = $serviceMethod->invoke(['jon', 'new'])->request();
        $this->assertSame('https://example.com/users/jon?group=new', $request->getUri()->__toString());
    }

    #[Test]
    public function shouldAddPathAndQueryName(): void
    {
        //when
        $serviceMethod = $this->serviceMethodFactory->create(FullyValidApi::class, 'addQueryName');

        //then
        $request = $serviceMethod->invoke(['user(jon)'])->request();
        $this->assertSame('https://example.com/users?user(jon)', $request->getUri()->__toString());
    }

    #[Test]
    public function shouldAddPathAndQueryMap(): void
    {
        //when
        $serviceMethod = $this->serviceMethodFactory->create(FullyValidApi::class, 'addQueryMap');

        //then
        $request = $serviceMethod->invoke([['name' => 'jon+doe', 'age' => 34, 'registered' => false]])->request();
        $this->assertSame('https://example.com/users?name=jon%2Bdoe&age=34&registered=false', $request->getUri()->__toString());
    }

    #[Test]
    public function shouldAddHeader(): void
    {
        //when
        $serviceMethod = $this->serviceMethodFactory->create(FullyValidApi::class, 'addHeader');

        //then
        $request = $serviceMethod->invoke(['some-custom-value'])->request();
        Assert::thatArray($request->getHeaders())->containsKeyAndValue(['x-custom' => ['some-custom-value']]);
    }

    #[Test]
    public function shouldAddHeaderMap(): void
    {
        //when
        $serviceMethod = $this->serviceMethodFactory->create(FullyValidApi::class, 'addHeaderMap');

        //then
        $request = $serviceMethod->invoke([['x-custom' => 'jon+doe', 'x-age' => 34, 'Content-Type' => 'application/json']])->request();
        Assert::thatArray($request->getHeaders())
            ->containsKeyAndValue(['x-custom' => ['jon+doe'], 'x-age' => ['34'], 'content-type' => ['application/json']]);
    }
}
