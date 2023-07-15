<?php
declare(strict_types=1);

namespace Retrofit\Tests\Internal;

use Closure;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Ouzo\Tests\CatchException;
use Ouzo\Tests\Mock\Mock;
use Ouzo\Tests\Mock\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Retrofit\Call;
use Retrofit\Callback;
use Retrofit\Converter\ResponseBodyConverter;
use Retrofit\HttpClient;
use Retrofit\HttpMethod;
use Retrofit\Internal\HttpClientCall;
use RuntimeException;
use Throwable;

class HttpClientCallTest extends TestCase
{
    private RequestInterface $request;
    private ResponseBodyConverter|MockInterface $responseBodyConverter;
    private ResponseBodyConverter|MockInterface $responseErrorBodyConverter;

    public function setUp(): void
    {
        parent::setUp();
        $this->request = new Request(HttpMethod::GET->value, '/users');
        $this->responseBodyConverter = Mock::create(ResponseBodyConverter::class);
        $this->responseErrorBodyConverter = Mock::create(ResponseBodyConverter::class);
    }

    #[Test]
    public function shouldThrowExceptionWhenCannotConvertBody(): void
    {
        //given
        $httpClient = self::MockHttpClient(new Response());
        Mock::when($this->responseBodyConverter)->convert(Mock::anyArgList())->thenThrow(new RuntimeException('cannot convert body'));

        $httpClientCall = new HttpClientCall($httpClient, $this->request, $this->responseBodyConverter, $this->responseErrorBodyConverter);

        //when
        CatchException::when($httpClientCall)->execute();

        //then
        CatchException::assertThat()
            ->isInstanceOf(RuntimeException::class)
            ->hasMessage('Retrofit: Could not convert response body.');
    }

    #[Test]
    public function shouldThrowExceptionWhenCannotConvertErrorBody(): void
    {
        //given
        $httpClient = self::MockHttpClient(new Response(400));
        Mock::when($this->responseErrorBodyConverter)->convert(Mock::anyArgList())->thenThrow(new RuntimeException('cannot convert body'));

        $httpClientCall = new HttpClientCall($httpClient, $this->request, $this->responseBodyConverter, $this->responseErrorBodyConverter);

        //when
        CatchException::when($httpClientCall)->execute();

        //then
        CatchException::assertThat()
            ->isInstanceOf(RuntimeException::class)
            ->hasMessage('Retrofit: Could not convert error body.');
    }

    #[Test]
    public function shouldHandleOnResponseAsyncCall(): void
    {
        //given
        $callback = self::MockCallback();
        $httpClient = self::MockHttpClient(new Response(200));

        $httpClientCall = new HttpClientCall($httpClient, $this->request, $this->responseBodyConverter, $this->responseErrorBodyConverter);

        //when
        $httpClientCall->enqueue($callback);
        $httpClientCall->wait();

        //then
        $this->assertTrue($callback::$onResponseCalled);
        $this->assertFalse($callback::$onFailureCalled);
    }

    #[Test]
    public function shouldHandleOnFailureAsyncCall(): void
    {
        //given
        $callback = self::MockCallback();
        $httpClient = self::MockHttpClient(new RuntimeException('something goes wrong'));

        $httpClientCall = new HttpClientCall($httpClient, $this->request, $this->responseBodyConverter, $this->responseErrorBodyConverter);

        //when
        $httpClientCall->enqueue($callback);
        $httpClientCall->wait();

        //then
        $this->assertFalse($callback::$onResponseCalled);
        $this->assertTrue($callback::$onFailureCalled);
    }

    #[Test]
    public function shouldHandleNullableResponseBodyConverter(): void
    {
        //given
        $response = new Response(200);
        $httpClient = self::MockHttpClient($response);

        $httpClientCall = new HttpClientCall($httpClient, $this->request, null, $this->responseErrorBodyConverter);

        //when
        $retrofitResponse = $httpClientCall->execute();

        //then
        $this->assertSame($response, $retrofitResponse->raw());
        $this->assertNull($retrofitResponse->body());
        $this->assertNull($retrofitResponse->errorBody());
    }

    private static function MockHttpClient(ResponseInterface|Throwable $result): HttpClient
    {
        return new class($result) implements HttpClient {
            private Closure $onResponse;
            private Closure $onFailure;

            public function __construct(private readonly ResponseInterface|Throwable $result)
            {
            }

            public function send(RequestInterface $request): ResponseInterface
            {
                return $this->result;
            }

            public function sendAsync(RequestInterface $request, Closure $onResponse, Closure $onFailure): void
            {
                $this->onResponse = $onResponse;
                $this->onFailure = $onFailure;
            }

            public function wait(): void
            {
                $closure = fn(ResponseInterface|Throwable $result) => null;

                if ($this->result instanceof ResponseInterface) {
                    $closure = $this->onResponse;
                }
                if ($this->result instanceof Throwable) {
                    $closure = $this->onFailure;
                }
                $closure($this->result);
            }
        };
    }

    private static function MockCallback(): Callback
    {
        return new class implements Callback {
            public static bool $onResponseCalled = false;
            public static bool $onFailureCalled = false;

            public function onResponse(Call $call, \Retrofit\Response $response): void
            {
                self::$onResponseCalled = true;
            }

            public function onFailure(Call $call, Throwable $t): void
            {
                self::$onFailureCalled = true;
            }
        };
    }
}
