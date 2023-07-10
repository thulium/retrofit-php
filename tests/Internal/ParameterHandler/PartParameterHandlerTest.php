<?php
declare(strict_types=1);

namespace Retrofit\Tests\Internal\ParameterHandler;

use GuzzleHttp\Psr7\Uri;
use Ouzo\Tests\CatchException;
use Ouzo\Utilities\Strings;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use Retrofit\Attribute\POST;
use Retrofit\Internal\BuiltInConverters;
use Retrofit\Internal\ParameterHandler\PartParameterHandler;
use Retrofit\Internal\RequestBuilder;
use Retrofit\MimeEncoding;
use Retrofit\Multipart\MultipartBody;
use Retrofit\Tests\Fixtures\Api\MockMethod;
use RuntimeException;

class PartParameterHandlerTest extends TestCase
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
        $partParameterHandler = new PartParameterHandler('part-nane', MimeEncoding::BINARY, BuiltInConverters::JsonEncodeRequestBodyConverter(), $this->reflectionMethod, 0);

        //when
        $partParameterHandler->apply($this->requestBuilder, null);

        //then
        $request = $this->requestBuilder->build();
        $this->assertSame(Strings::EMPTY, $request->getBody()->getContents());
    }

    #[Test]
    public function shouldThrowExceptionWhenNameIsBlankAndTypeIsNotPartInterface(): void
    {
        //given
        $partParameterHandler = new PartParameterHandler('', MimeEncoding::BINARY, BuiltInConverters::JsonEncodeRequestBodyConverter(), $this->reflectionMethod, 0);

        //when
        CatchException::when($partParameterHandler)->apply($this->requestBuilder, 'some-value');

        //then
        CatchException::assertThat()
            ->isInstanceOf(RuntimeException::class)
            ->hasMessage('Method MockMethod::mockMethod() parameter #1. #[Part] attribute must supply a name or use MultipartBody.Part parameter type.');
    }

    #[Test]
    public function shouldThrowExceptionWhenNameIsNotBlankAndTypeIsPartInterface(): void
    {
        //given
        $partParameterHandler = new PartParameterHandler('part-name', MimeEncoding::BINARY, BuiltInConverters::JsonEncodeRequestBodyConverter(), $this->reflectionMethod, 0);
        $part = MultipartBody::Part()::createFromData('part-name-from-object', 'body');

        //when
        CatchException::when($partParameterHandler)->apply($this->requestBuilder, $part);

        //then
        CatchException::assertThat()
            ->isInstanceOf(RuntimeException::class)
            ->hasMessage('Method MockMethod::mockMethod() parameter #1. #[Part] attribute using the MultipartBody.Part must not include a part name in the attribute.');
    }
}
