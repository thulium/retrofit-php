<?php

declare(strict_types=1);

namespace Retrofit\Tests\Core\Internal\ParameterHandler;

use GuzzleHttp\Psr7\Uri;
use Ouzo\Tests\Assert;
use Ouzo\Tests\CatchException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use Retrofit\Core\Attribute\GET;
use Retrofit\Core\Internal\BuiltInConverters;
use Retrofit\Core\Internal\ParameterHandler\HeaderMapParameterHandler;
use Retrofit\Core\Internal\RequestBuilder;
use Retrofit\Tests\Fixtures\Api\MockMethod;
use RuntimeException;

class HeaderMapParameterHandlerTest extends TestCase
{
    private RequestBuilder $requestBuilder;
    private ReflectionMethod $reflectionMethod;

    public function setUp(): void
    {
        parent::setUp();
        $this->requestBuilder = new RequestBuilder(new Uri('https://example.com'), new GET('/users'));
        $this->reflectionMethod = new ReflectionMethod(MockMethod::class, 'mockMethod');
    }

    #[Test]
    public function shouldThrowExceptionWhenValueIsNull(): void
    {
        //given
        $headerMapParameterHandler = new HeaderMapParameterHandler(BuiltInConverters::ToStringConverter(), $this->reflectionMethod, 0);

        //when
        CatchException::when($headerMapParameterHandler)->apply($this->requestBuilder, null);

        //then
        CatchException::assertThat()
            ->isInstanceOf(RuntimeException::class)
            ->hasMessage('Method MockMethod::mockMethod() parameter #1. Header map was null.');
    }

    #[Test]
    public function shouldThrowExceptionWhenValueIsNotArray(): void
    {
        //given
        $headerMapParameterHandler = new HeaderMapParameterHandler(BuiltInConverters::ToStringConverter(), $this->reflectionMethod, 0);

        //when
        CatchException::when($headerMapParameterHandler)->apply($this->requestBuilder, 'some-string-value');

        //then
        CatchException::assertThat()
            ->isInstanceOf(RuntimeException::class)
            ->hasMessage('Method MockMethod::mockMethod() parameter #1. Parameter should be an array.');
    }

    #[Test]
    public function shouldThrowExceptionWhenKeyInArrayIsNull(): void
    {
        //given
        $headerMapParameterHandler = new HeaderMapParameterHandler(BuiltInConverters::ToStringConverter(), $this->reflectionMethod, 0);

        //when
        CatchException::when($headerMapParameterHandler)->apply($this->requestBuilder, [null => 'value']);

        //then
        CatchException::assertThat()
            ->isInstanceOf(RuntimeException::class)
            ->hasMessage('Method MockMethod::mockMethod() parameter #1. Header map contained empty key.');
    }

    #[Test]
    public function shouldThrowExceptionWhenValueInArrayIsNull(): void
    {
        //given
        $headerMapParameterHandler = new HeaderMapParameterHandler(BuiltInConverters::ToStringConverter(), $this->reflectionMethod, 0);

        //when
        CatchException::when($headerMapParameterHandler)->apply($this->requestBuilder, ['key' => null]);

        //then
        CatchException::assertThat()
            ->isInstanceOf(RuntimeException::class)
            ->hasMessage("Method MockMethod::mockMethod() parameter #1. Header map contained null value for key 'key'.");
    }

    #[Test]
    public function shouldAddHeaders(): void
    {
        //given
        $headerMapParameterHandler = new HeaderMapParameterHandler(BuiltInConverters::ToStringConverter(), $this->reflectionMethod, 0);

        //when
        $headerMapParameterHandler->apply($this->requestBuilder, ['x-custom' => 'jon+doe', 'x-age' => 34, 'Content-Type' => 'application/json']);

        //then
        $request = $this->requestBuilder->build();
        Assert::thatArray($request->getHeaders())
            ->containsKeyAndValue(['x-custom' => ['jon+doe'], 'x-age' => ['34'], 'content-type' => ['application/json']]);
    }
}
