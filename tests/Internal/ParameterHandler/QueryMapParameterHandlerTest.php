<?php
declare(strict_types=1);

namespace Retrofit\Tests\Internal\ParameterHandler;

use GuzzleHttp\Psr7\Uri;
use Ouzo\Tests\CatchException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use Retrofit\Attribute\GET;
use Retrofit\Internal\BuiltInConverters;
use Retrofit\Internal\ParameterHandler\QueryMapParameterHandler;
use Retrofit\Internal\RequestBuilder;
use Retrofit\Tests\Fixtures\Api\QueryMapAttribute;
use RuntimeException;

class QueryMapParameterHandlerTest extends TestCase
{
    private RequestBuilder $requestBuilder;
    private ReflectionMethod $reflectionMethod;

    public function setUp(): void
    {
        parent::setUp();
        $this->requestBuilder = new RequestBuilder(new Uri('https://example.com'), new GET('/users'));
        $this->reflectionMethod = new ReflectionMethod(QueryMapAttribute::class, 'nullableParameter');
    }

    #[Test]
    public function shouldSkipWhenValueIsNull(): void
    {
        //given
        $queryMapParameterHandler = new QueryMapParameterHandler(false, BuiltInConverters::toStringConverter(), $this->reflectionMethod, 0);

        //when
        $queryMapParameterHandler->apply($this->requestBuilder, null);

        //then
        $request = $this->requestBuilder->build();
        $this->assertSame('https://example.com/users', $request->getUri()->__toString());
    }

    #[Test]
    public function shouldThrowExceptionWhenValueIsNotArray(): void
    {
        //given
        $queryMapParameterHandler = new QueryMapParameterHandler(false, BuiltInConverters::toStringConverter(), $this->reflectionMethod, 0);

        //when
        CatchException::when($queryMapParameterHandler)->apply($this->requestBuilder, 'some-string-value');

        //then
        CatchException::assertThat()
            ->isInstanceOf(RuntimeException::class)
            ->hasMessage('Method QueryMapAttribute::nullableParameter() parameter #1. Parameter should be an array.');
    }

    #[Test]
    public function shouldThrowExceptionWhenKeyInArrayIsNull(): void
    {
        //given
        $queryMapParameterHandler = new QueryMapParameterHandler(false, BuiltInConverters::toStringConverter(), $this->reflectionMethod, 0);

        //when
        CatchException::when($queryMapParameterHandler)->apply($this->requestBuilder, [null => 'value']);

        //then
        CatchException::assertThat()
            ->isInstanceOf(RuntimeException::class)
            ->hasMessage('Method QueryMapAttribute::nullableParameter() parameter #1. Query map contained empty key.');
    }

    #[Test]
    public function shouldThrowExceptionWhenValueInArrayIsNull(): void
    {
        //given
        $queryMapParameterHandler = new QueryMapParameterHandler(false, BuiltInConverters::toStringConverter(), $this->reflectionMethod, 0);

        //when
        CatchException::when($queryMapParameterHandler)->apply($this->requestBuilder, ['key' => null]);

        //then
        CatchException::assertThat()
            ->isInstanceOf(RuntimeException::class)
            ->hasMessage("Method QueryMapAttribute::nullableParameter() parameter #1. Query map contained null value for key 'key'.");
    }

    #[Test]
    public function shouldAddEncodedQueries(): void
    {
        //given
        $queryMapParameterHandler = new QueryMapParameterHandler(false, BuiltInConverters::toStringConverter(), $this->reflectionMethod, 0);

        //when
        $queryMapParameterHandler->apply($this->requestBuilder, ['name' => 'jon+doe', 'age' => 34, 'registered' => false]);

        //then
        $request = $this->requestBuilder->build();
        $this->assertSame('https://example.com/users?name=jon%2Bdoe&age=34&registered=false', $request->getUri()->__toString());
    }

    #[Test]
    public function shouldAddNotEncodedQueries(): void
    {
        //given
        $queryMapParameterHandler = new QueryMapParameterHandler(true, BuiltInConverters::toStringConverter(), $this->reflectionMethod, 0);

        //when
        $queryMapParameterHandler->apply($this->requestBuilder, ['name' => 'jon+doe', 'age' => 34, 'registered' => false]);

        //then
        $request = $this->requestBuilder->build();
        $this->assertSame('https://example.com/users?name=jon+doe&age=34&registered=false', $request->getUri()->__toString());
    }
}
