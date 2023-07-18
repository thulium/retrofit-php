<?php

declare(strict_types=1);

namespace Retrofit\Tests\Core\Internal\ParameterHandler;

use GuzzleHttp\Psr7\Uri;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use Retrofit\Core\Attribute\POST;
use Retrofit\Core\Internal\BuiltInConverters;
use Retrofit\Core\Internal\ParameterHandler\QueryNameParameterHandler;
use Retrofit\Core\Internal\RequestBuilder;
use Retrofit\Tests\Fixtures\Api\MockMethod;

class QueryNameParameterHandlerTest extends TestCase
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
        // given
        $queryParameterHandler = new QueryNameParameterHandler(false, BuiltInConverters::ToStringConverter(), $this->reflectionMethod, 0);

        // when
        $queryParameterHandler->apply($this->requestBuilder, null);

        // then
        $request = $this->requestBuilder->build();
        $this->assertSame('https://example.com/users', $request->getUri()->__toString());
    }

    #[Test]
    public function shouldAddEncodedQueryName(): void
    {
        // given
        $queryNameParameterHandler = new QueryNameParameterHandler(true, BuiltInConverters::ToStringConverter(), $this->reflectionMethod, 0);

        // when
        $queryNameParameterHandler->apply($this->requestBuilder, 'contains(Bob)');

        // then
        $request = $this->requestBuilder->build();
        $this->assertSame('https://example.com/users?contains(Bob)', $request->getUri()->__toString());
    }

    #[Test]
    public function shouldAddArrayOfEncodedQueryName(): void
    {
        // given
        $queryNameParameterHandler = new QueryNameParameterHandler(true, BuiltInConverters::ToStringConverter(), $this->reflectionMethod, 0);

        // when
        $queryNameParameterHandler->apply($this->requestBuilder, ['contains(Bob)', 'age(20)']);

        // then
        $request = $this->requestBuilder->build();
        $this->assertSame('https://example.com/users?contains(Bob)&age(20)', $request->getUri()->__toString());
    }

    #[Test]
    public function shouldAddQueryName(): void
    {
        // given
        $queryNameParameterHandler = new QueryNameParameterHandler(false, BuiltInConverters::ToStringConverter(), $this->reflectionMethod, 0);

        // when
        $queryNameParameterHandler->apply($this->requestBuilder, 'contains(Bob)');

        // then
        $request = $this->requestBuilder->build();
        $this->assertSame('https://example.com/users?contains%28Bob%29', $request->getUri()->__toString());
    }

    #[Test]
    public function shouldAddArrayOfQueryName(): void
    {
        // given
        $queryNameParameterHandler = new QueryNameParameterHandler(false, BuiltInConverters::ToStringConverter(), $this->reflectionMethod, 0);

        // when
        $queryNameParameterHandler->apply($this->requestBuilder, ['contains(Bob)', 'age(20)']);

        // then
        $request = $this->requestBuilder->build();
        $this->assertSame('https://example.com/users?contains%28Bob%29&age%2820%29', $request->getUri()->__toString());
    }
}
