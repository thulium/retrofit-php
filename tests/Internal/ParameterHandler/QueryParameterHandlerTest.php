<?php
declare(strict_types=1);

namespace Retrofit\Tests\Internal\ParameterHandler;

use GuzzleHttp\Psr7\Uri;
use Ouzo\Tests\CatchException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use Retrofit\Attribute\POST;
use Retrofit\Internal\BuiltInConverters;
use Retrofit\Internal\ParameterHandler\QueryParameterHandler;
use Retrofit\Internal\RequestBuilder;
use Retrofit\Tests\Fixtures\Api\MockMethod;
use RuntimeException;
use stdClass;

class QueryParameterHandlerTest extends TestCase
{
    private RequestBuilder $requestBuilder;
    private ReflectionMethod $reflectionMethod;

    public function setUp(): void
    {
        parent::setUp();
        $this->requestBuilder = new RequestBuilder(new Uri('https://example.com'), new POST('/users'));
        $this->reflectionMethod = new ReflectionMethod(MockMethod::class, 'mockMethod');
    }

    #[Test]
    public function shouldSkipNullValues(): void
    {
        //given
        $queryParameterHandler = new QueryParameterHandler('group', false, BuiltInConverters::toStringConverter(), $this->reflectionMethod, 0);

        //when
        $queryParameterHandler->apply($this->requestBuilder, null);

        //then
        $request = $this->requestBuilder->build();
        $this->assertSame('https://example.com/users', $request->getUri()->__toString());
    }

    #[Test]
    public function shouldAddQueryParam(): void
    {
        //given
        $queryParameterHandler = new QueryParameterHandler('group', false, BuiltInConverters::toStringConverter(), $this->reflectionMethod, 0);

        //when
        $queryParameterHandler->apply($this->requestBuilder, 'new+users');

        //then
        $request = $this->requestBuilder->build();
        $this->assertSame('https://example.com/users?group=new%2Busers', $request->getUri()->__toString());
    }

    #[Test]
    public function shouldAddEncodedQueryParam(): void
    {
        //given
        $queryParameterHandler = new QueryParameterHandler('group', true, BuiltInConverters::toStringConverter(), $this->reflectionMethod, 0);

        //when
        $queryParameterHandler->apply($this->requestBuilder, 'new+users');

        //then
        $request = $this->requestBuilder->build();
        $this->assertSame('https://example.com/users?group=new+users', $request->getUri()->__toString());
    }

    #[Test]
    public function shouldAddArrayOfQueryParams(): void
    {
        //given
        $queryParameterHandler = new QueryParameterHandler('groups', false, BuiltInConverters::toStringConverter(), $this->reflectionMethod, 0);

        //when
        $queryParameterHandler->apply($this->requestBuilder, ['new+users', 'old']);

        //then
        $request = $this->requestBuilder->build();
        $this->assertSame('https://example.com/users?groups=new%2Busers&groups=old', $request->getUri()->__toString());
    }

    #[Test]
    public function shouldAddEncodedArrayOfQueryParams(): void
    {
        //given
        $queryParameterHandler = new QueryParameterHandler('groups', true, BuiltInConverters::toStringConverter(), $this->reflectionMethod, 0);

        //when
        $queryParameterHandler->apply($this->requestBuilder, ['new+users', 'old']);

        //then
        $request = $this->requestBuilder->build();
        $this->assertSame('https://example.com/users?groups=new+users&groups=old', $request->getUri()->__toString());
    }

    #[Test]
    public function shouldThrowExceptionWhenPassedArrayIsNotAList(): void
    {
        //given
        $queryParameterHandler = new QueryParameterHandler('groups', true, BuiltInConverters::toStringConverter(), $this->reflectionMethod, 0);

        //when
        CatchException::when($queryParameterHandler)->apply($this->requestBuilder, ['key1' => 'new+users', 'old']);

        //then
        CatchException::assertThat()
            ->isInstanceOf(RuntimeException::class)
            ->hasMessage('Method MockMethod::mockMethod() parameter #1. Parameter must be a list.');
    }

    #[Test]
    public function shouldThrowExceptionWhenPassedArrayHasObject(): void
    {
        //given
        $queryParameterHandler = new QueryParameterHandler('groups', true, BuiltInConverters::toStringConverter(), $this->reflectionMethod, 0);

        $stdClass = new stdClass();

        //when
        CatchException::when($queryParameterHandler)->apply($this->requestBuilder, ['new+users', $stdClass]);

        //then
        CatchException::assertThat()
            ->isInstanceOf(RuntimeException::class)
            ->hasMessage('Method MockMethod::mockMethod() parameter #1. One of the list value is an object.');
    }
}
