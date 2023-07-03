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
use Retrofit\Internal\ParameterHandler\PathParameterHandler;
use Retrofit\Internal\RequestBuilder;
use Retrofit\Tests\Fixtures\Api\FullyValidApi;
use RuntimeException;

class PathParameterHandlerTest extends TestCase
{
    private RequestBuilder $requestBuilder;
    private ReflectionMethod $reflectionMethod;

    public function setUp(): void
    {
        parent::setUp();
        $this->requestBuilder = new RequestBuilder(new Uri('https://example.com'), new POST('/users/{login}'));
        $this->reflectionMethod = new ReflectionMethod(FullyValidApi::class, 'createUser');
    }

    #[Test]
    public function shouldThrowExceptionWhenValueIsNull(): void
    {
        //given
        $pathParameterHandler = new PathParameterHandler('login', false, BuiltInConverters::toStringConverter(), $this->reflectionMethod, 0);

        //when
        CatchException::when($pathParameterHandler)->apply($this->requestBuilder, null);

        //then
        CatchException::assertThat()
            ->isInstanceOf(RuntimeException::class)
            ->hasMessage("Method FullyValidApi::createUser() parameter #1. #[Path] parameter 'login' value must not be null.");
    }

    #[Test]
    public function shouldReplaceNotEncodedValue(): void
    {
        //given
        $pathParameterHandler = new PathParameterHandler('login', false, BuiltInConverters::toStringConverter(), $this->reflectionMethod, 0);

        //when
        $pathParameterHandler->apply($this->requestBuilder, 'Jon+Doe');

        //then
        $request = $this->requestBuilder->build();
        $this->assertSame('https://example.com/users/Jon%2BDoe', $request->getUri()->__toString());
    }

    #[Test]
    public function shouldReplaceEncodedValue(): void
    {
        //given
        $pathParameterHandler = new PathParameterHandler('login', true, BuiltInConverters::toStringConverter(), $this->reflectionMethod, 0);

        //when
        $pathParameterHandler->apply($this->requestBuilder, 'Jon+Doe');

        //then
        $request = $this->requestBuilder->build();
        $this->assertSame('https://example.com/users/Jon+Doe', $request->getUri()->__toString());
    }
}
