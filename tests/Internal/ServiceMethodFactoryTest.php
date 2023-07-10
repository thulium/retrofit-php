<?php
declare(strict_types=1);

namespace Retrofit\Tests\Internal;

use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Psr7\Utils;
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
use Retrofit\Internal\Encoding;
use Retrofit\Internal\ParameterHandler\Factory\AbstractParameterHandlerFactory;
use Retrofit\Internal\ParameterHandler\Factory\ParameterHandlerFactoryProvider;
use Retrofit\Internal\ParameterHandler\ParameterHandler;
use Retrofit\Internal\Proxy\DefaultProxyFactory;
use Retrofit\Internal\ServiceMethodFactory;
use Retrofit\Multipart\MultipartBody;
use Retrofit\Retrofit;
use Retrofit\Tests\Fixtures\Api\AllHttpRequestMethods;
use Retrofit\Tests\Fixtures\Api\FullyValidApi;
use Retrofit\Tests\Fixtures\Api\InvalidMethods;
use Retrofit\Tests\Fixtures\Model\UserRequest;
use RuntimeException;

class ServiceMethodFactoryTest extends TestCase
{
    private Retrofit $retrofit;
    private ParameterHandlerFactoryProvider|MockInterface $parameterHandlerFactoryProvider;
    private ServiceMethodFactory $serviceMethodFactory;

    public function setUp(): void
    {
        parent::setUp();
        /** @var HttpClient|MockInterface $httpClient */
        $httpClient = Mock::create(HttpClient::class);
        $baseUrl = new Uri('https://example.com');
        $converterProvider = new ConverterProvider([new BuiltInConverterFactory()]);
        $proxyFactory = new DefaultProxyFactory(new BuilderFactory(), new Standard());

        $this->retrofit = new Retrofit($httpClient, $baseUrl, $converterProvider, $proxyFactory);

        $this->parameterHandlerFactoryProvider = new ParameterHandlerFactoryProvider($this->retrofit->converterProvider);
        $this->serviceMethodFactory = new ServiceMethodFactory($this->retrofit, $this->parameterHandlerFactoryProvider);
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

    #[Test]
    public function shouldSetDefaultHeaders(): void
    {
        //when
        $serviceMethod = $this->serviceMethodFactory->create(FullyValidApi::class, 'addHeaders');

        //then
        $request = $serviceMethod->invoke([['x-custom' => 'jon+doe', 'x-age' => 34, 'Content-Type' => 'application/json']])->request();
        Assert::thatArray($request->getHeaders())
            ->containsKeyAndValue(['x-custom' => ['jon+doe'], 'x-age' => ['34'], 'content-type' => ['application/json']]);
    }

    #[Test]
    public function shouldParameterHeaderHaveHighestPrecedenceThanMethodHeaders(): void
    {
        //when
        $serviceMethod = $this->serviceMethodFactory->create(FullyValidApi::class, 'addHeadersWithParameterHeader');

        //then
        $request = $serviceMethod->invoke([100])->request();
        Assert::thatArray($request->getHeaders())
            ->containsKeyAndValue(['x-custom' => ['jon+doe'], 'x-age' => ['100'], 'content-type' => ['application/json']]);
    }

    #[Test]
    public function shouldThrowExceptionWhenKeyInHeadersAttributeIsNull(): void
    {
        //when
        CatchException::when($this->serviceMethodFactory)->create(InvalidMethods::class, 'headersKeyIsNull');

        //then
        CatchException::assertThat()
            ->isInstanceOf(RuntimeException::class)
            ->hasMessage('Method InvalidMethods::headersKeyIsNull(). Headers map contained empty key.');
    }

    #[Test]
    public function shouldThrowExceptionWhenValueInHeadersAttributeIsNull(): void
    {
        //when
        CatchException::when($this->serviceMethodFactory)->create(InvalidMethods::class, 'headersValueIsNull');

        //then
        CatchException::assertThat()
            ->isInstanceOf(RuntimeException::class)
            ->hasMessage("Method InvalidMethods::headersValueIsNull(). Headers map contained null value for key 'key'.");
    }

    #[Test]
    public function shouldThrowExceptionWhenMultipleEncodingDefined(): void
    {
        //when
        CatchException::when($this->serviceMethodFactory)->create(InvalidMethods::class, 'multipleEncodings');

        //then
        CatchException::assertThat()
            ->isInstanceOf(RuntimeException::class)
            ->hasMessage('Method InvalidMethods::multipleEncodings(). Only one encoding annotation is allowed.');
    }

    #[Test]
    public function shouldPassNullEncodingWhenMethodIsNotImplementingAny(): void
    {
        //given
        $factory = Mock::create(AbstractParameterHandlerFactory::class);
        Mock::when($factory)->create(Mock::anyArgList())->thenReturn(Mock::create(ParameterHandler::class));

        $this->parameterHandlerFactoryProvider = Mock::create(ParameterHandlerFactoryProvider::class);
        Mock::when($this->parameterHandlerFactoryProvider)->get(Mock::anyArgList())->thenReturn($factory);

        $serviceMethodFactory = new ServiceMethodFactory($this->retrofit, $this->parameterHandlerFactoryProvider);

        //when
        $serviceMethodFactory->create(FullyValidApi::class, 'addHeader');

        //then
        Mock::verify($factory)->create(Mock::any(), Mock::any(), null, Mock::any(), Mock::any());
    }

    #[Test]
    public function shouldPassFormUrlEncodedEncodingWhenMethodIsNotImplementingFormUrlEncodedAttribute(): void
    {
        //given
        $factory = Mock::create(AbstractParameterHandlerFactory::class);
        Mock::when($factory)->create(Mock::anyArgList())->thenReturn(Mock::create(ParameterHandler::class));

        $this->parameterHandlerFactoryProvider = Mock::create(ParameterHandlerFactoryProvider::class);
        Mock::when($this->parameterHandlerFactoryProvider)->get(Mock::anyArgList())->thenReturn($factory);

        $serviceMethodFactory = new ServiceMethodFactory($this->retrofit, $this->parameterHandlerFactoryProvider);

        //when
        $serviceMethodFactory->create(FullyValidApi::class, 'formUrlEncoded');

        //then
        Mock::verify($factory)->create(Mock::any(), Mock::any(), Encoding::FORM_URL_ENCODED, Mock::any(), Mock::any());
    }

    #[Test]
    public function shouldPassMultipartEncodingWhenMethodIsNotImplementingFormUrlEncodedAttribute(): void
    {
        //given
        $factory = Mock::create(AbstractParameterHandlerFactory::class);
        Mock::when($factory)->create(Mock::anyArgList())->thenReturn(Mock::create(ParameterHandler::class));

        $this->parameterHandlerFactoryProvider = Mock::create(ParameterHandlerFactoryProvider::class);
        Mock::when($this->parameterHandlerFactoryProvider)->get(Mock::anyArgList())->thenReturn($factory);

        $serviceMethodFactory = new ServiceMethodFactory($this->retrofit, $this->parameterHandlerFactoryProvider);

        //when
        $serviceMethodFactory->create(FullyValidApi::class, 'multipart');

        //then
        Mock::verify($factory)->create(Mock::any(), Mock::any(), Encoding::MULTIPART, Mock::any(), Mock::any());
    }

    #[Test]
    public function shouldThrowExceptionWhenMultipartIsNotForHttpMethodWithBody(): void
    {
        //when
        CatchException::when($this->serviceMethodFactory)->create(InvalidMethods::class, 'multipartForHttpMethodWithoutBody');

        //then
        CatchException::assertThat()
            ->isInstanceOf(RuntimeException::class)
            ->hasMessage('Method InvalidMethods::multipartForHttpMethodWithoutBody(). #[Multipart] can only be specified on HTTP methods with request body (e.g., #[POST]).');
    }

    #[Test]
    public function shouldThrowExceptionWhenFormUrlEncodedIsNotForHttpMethodWithBody(): void
    {
        //when
        CatchException::when($this->serviceMethodFactory)->create(InvalidMethods::class, 'formUrlEncodedForHttpMethodWithoutBody');

        //then
        CatchException::assertThat()
            ->isInstanceOf(RuntimeException::class)
            ->hasMessage('Method InvalidMethods::formUrlEncodedForHttpMethodWithoutBody(). #[FormUrlEncoded] can only be specified on HTTP methods with request body (e.g., #[POST]).');
    }

    #[Test]
    public function shouldAddField(): void
    {
        //when
        $serviceMethod = $this->serviceMethodFactory->create(FullyValidApi::class, 'addField');

        //then
        $request = $serviceMethod->invoke(['jon+doe', 'users+admin'])->request();
        $this->assertSame('x-login=jon%2Bdoe&filters=users+admin', $request->getBody()->getContents());
    }

    #[Test]
    public function shouldAddFieldMap(): void
    {
        //when
        $serviceMethod = $this->serviceMethodFactory->create(FullyValidApi::class, 'addFieldMap');

        //then
        $request = $serviceMethod->invoke([['x-login' => 'jon+doe', 'filters' => 'users+admin']])->request();
        $this->assertSame('x-login=jon+doe&filters=users+admin', $request->getBody()->getContents());
    }

    #[Test]
    public function shouldThrowExceptionWhenFormUrlEncodedDoesNotHaveAtLeastOneFieldAttribute(): void
    {
        //when
        CatchException::when($this->serviceMethodFactory)->create(InvalidMethods::class, 'formUrlEncodedDoesNotHaveAtLeastOneFieldAttribute');

        //then
        CatchException::assertThat()
            ->isInstanceOf(RuntimeException::class)
            ->hasMessage('Method InvalidMethods::formUrlEncodedDoesNotHaveAtLeastOneFieldAttribute(). #[FormUrlEncoded] method must contain at least one #[Field] or #[FieldMap].');
    }

    #[Test]
    public function shouldThrowExceptionWhenMultipartDoesNotHaveAtLeastOnePartAttribute(): void
    {
        //when
        CatchException::when($this->serviceMethodFactory)->create(InvalidMethods::class, 'multipartDoesNotHaveAtLeastOnePartAttribute');

        //then
        CatchException::assertThat()
            ->isInstanceOf(RuntimeException::class)
            ->hasMessage('Method InvalidMethods::multipartDoesNotHaveAtLeastOnePartAttribute(). #[Multipart] method must contain at least one #[Part] or #[PartMap].');
    }

    #[Test]
    public function shouldAddPart(): void
    {
        //given
        $string = 'some-string';
        $userRequest = (new UserRequest())
            ->setLogin('jon-doe');
        $partInterface = MultipartBody::Part()::createFromData('part-iface', Utils::streamFor(fopen('/tmp/image.png', 'r')), [], 'image.png');
        $streamInterface = Utils::streamFor(fopen('/tmp/image.png', 'r'));

        //when
        $serviceMethod = $this->serviceMethodFactory->create(FullyValidApi::class, 'addPart');

        //then
        $request = $serviceMethod->invoke([$string, $userRequest, $partInterface, $streamInterface])->request();

        $contents = $request->getBody()->getContents();

        $part1 = "Content-Transfer-Encoding: binary\r\nContent-Disposition: form-data; name=\"string\"\r\nContent-Length: 13\r\n\r\n\"some-string\"";
        $this->assertStringContainsString($part1, $contents);

        $part2 = "Content-Transfer-Encoding: binary\r\nContent-Disposition: form-data; name=\"userRequest\"\r\nContent-Length: 19\r\n\r\n{\"login\":\"jon-doe\"}\r\n";
        $this->assertStringContainsString($part2, $contents);

        $part3 = "Content-Transfer-Encoding: binary\r\nContent-Disposition: form-data; name=\"part-iface\"; filename=\"image.png\"\r\nContent-Type: image/png\r\n\r\n\r\n";
        $this->assertStringContainsString($part3, $contents);

        $part4 = "Content-Transfer-Encoding: binary\r\nContent-Disposition: form-data; name=\"stream\"\r\nContent-Length: 2\r\n\r\n{}\r\n";
        $this->assertStringContainsString($part4, $contents);
    }
}
