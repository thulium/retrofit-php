<?php
declare(strict_types=1);

namespace Retrofit\Tests\Core\Internal\ParameterHandler;

use GuzzleHttp\Psr7\Uri;
use Ouzo\Tests\CatchException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use Retrofit\Core\Attribute\GET;
use Retrofit\Core\Internal\ParameterHandler\UrlParameterHandler;
use Retrofit\Core\Internal\RequestBuilder;
use Retrofit\Tests\Fixtures\Api\MockMethod;
use RuntimeException;

class UrlParameterHandlerTest extends TestCase
{
    private RequestBuilder $requestBuilder;
    private UrlParameterHandler $urlParameterHandler;

    public function setUp(): void
    {
        parent::setUp();
        $this->requestBuilder = new RequestBuilder(new Uri('https://example.com'), new GET('/users/{name}'));
        $reflectionMethod = new ReflectionMethod(MockMethod::class, 'mockMethod');
        $this->urlParameterHandler = new UrlParameterHandler($reflectionMethod, 0);
    }

    #[Test]
    public function shouldThrowExceptionWhenValueIsNull(): void
    {
        //when
        CatchException::when($this->urlParameterHandler)->apply($this->requestBuilder, null);

        //then
        CatchException::assertThat()
            ->isInstanceOf(RuntimeException::class)
            ->hasMessage('Method MockMethod::mockMethod() parameter #1. #[Url] parameter value must not be null.');
    }

    #[Test]
    public function shouldReplaceUrlUsingPassedInRuntime(): void
    {
        //when
        $this->urlParameterHandler->apply($this->requestBuilder, 'https://foo.bar/v2/api');

        //then
        $request = $this->requestBuilder->build();
        $this->assertSame('https://foo.bar/v2/api', $request->getUri()->__toString());
    }
}
