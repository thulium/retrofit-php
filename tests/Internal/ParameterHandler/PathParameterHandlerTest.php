<?php
declare(strict_types=1);

namespace Retrofit\Tests\Internal\ParameterHandler;

use Nyholm\Psr7\Uri;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use Retrofit\Attribute\GET;
use Retrofit\Internal\BuiltInConverters;
use Retrofit\Internal\ParameterHandler\PathParameterHandler;
use Retrofit\Internal\RequestBuilder;
use Retrofit\Tests\Fixtures\SomeApi;
use RuntimeException;

class PathParameterHandlerTest extends TestCase
{
    private RequestBuilder $requestBuilder;
    private ReflectionMethod $reflectionMethod;

    public function setUp(): void
    {
        parent::setUp();
        $this->requestBuilder = new RequestBuilder(new Uri('https://example.com'), new GET('/users/{name}'));
        $this->reflectionMethod = new ReflectionMethod(SomeApi::class, 'getUserByName');
    }

    #[Test]
    public function shouldThrowExceptionWhenValueIsNull(): void
    {
        //given
        $pathParameterHandler = new PathParameterHandler('name', false, BuiltInConverters::toStringConverter(), $this->reflectionMethod, 0);

        //when
        try {
            $pathParameterHandler->apply($this->requestBuilder, null);
            //then
        } catch (RuntimeException $e) {
            $this->assertSame("Method SomeApi::getUserByName() parameter #1. #[Path] parameter 'name' value must not be null.", $e->getMessage());
        }
    }

    #[Test]
    public function shouldReplaceNotEncodedValue(): void
    {
        //given
        $pathParameterHandler = new PathParameterHandler('name', false, BuiltInConverters::toStringConverter(), $this->reflectionMethod, 0);

        //when
        $pathParameterHandler->apply($this->requestBuilder, 'Jon+Doe');

        //then
        $request = $this->requestBuilder->build();
        $this->assertSame('https://example.com/users/Jon+Doe', $request->getUri()->__toString());
    }

    #[Test]
    public function shouldReplaceEncodedValue(): void
    {
        //given
        $pathParameterHandler = new PathParameterHandler('name', true, BuiltInConverters::toStringConverter(), $this->reflectionMethod, 0);

        //when
        $pathParameterHandler->apply($this->requestBuilder, 'Jon+Doe');

        //then
        $request = $this->requestBuilder->build();
        $this->assertSame('https://example.com/users/Jon%2BDoe', $request->getUri()->__toString());
    }
}
