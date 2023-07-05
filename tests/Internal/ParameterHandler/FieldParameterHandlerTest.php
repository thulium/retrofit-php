<?php
declare(strict_types=1);

namespace Retrofit\Tests\Internal\ParameterHandler;

use GuzzleHttp\Psr7\Uri;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use Retrofit\Attribute\GET;
use Retrofit\Internal\BuiltInConverters;
use Retrofit\Internal\ParameterHandler\FieldParameterHandler;
use Retrofit\Internal\RequestBuilder;
use Retrofit\Tests\Fixtures\Api\MockMethod;

class FieldParameterHandlerTest extends TestCase
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
    public function shouldSkipNullValues(): void
    {
        //given
        $fieldParameterHandler = new FieldParameterHandler('name', false, BuiltInConverters::toStringConverter());

        //when
        $fieldParameterHandler->apply($this->requestBuilder, null);

        //then
        $request = $this->requestBuilder->build();
        $this->assertSame('https://example.com/users', $request->getUri()->__toString());
    }

    #[Test]
    public function shouldAddNotEncodedFormField(): void
    {
        //given
        $fieldParameterHandler = new FieldParameterHandler('name', false, BuiltInConverters::toStringConverter());

        //when
        $fieldParameterHandler->apply($this->requestBuilder, 'jon+doe');

        //then
        $request = $this->requestBuilder->build();
        $this->assertSame('https://example.com/users', $request->getUri()->__toString());
        $this->assertSame('name=jon%2Bdoe', $request->getBody()->getContents());
    }

    #[Test]
    public function shouldAddEncodedFormField(): void
    {
        //given
        $fieldParameterHandler = new FieldParameterHandler('name', true, BuiltInConverters::toStringConverter());

        //when
        $fieldParameterHandler->apply($this->requestBuilder, 'jon+doe');

        //then
        $request = $this->requestBuilder->build();
        $this->assertSame('https://example.com/users', $request->getUri()->__toString());
        $this->assertSame('name=jon+doe', $request->getBody()->getContents());
    }
}
