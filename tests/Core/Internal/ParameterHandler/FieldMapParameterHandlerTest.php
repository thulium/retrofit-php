<?php

declare(strict_types=1);

namespace Retrofit\Tests\Core\Internal\ParameterHandler;

use GuzzleHttp\Psr7\Uri;
use Ouzo\Tests\CatchException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use Retrofit\Core\Attribute\GET;
use Retrofit\Core\Internal\BuiltInConverters;
use Retrofit\Core\Internal\ParameterHandler\FieldMapParameterHandler;
use Retrofit\Core\Internal\RequestBuilder;
use Retrofit\Tests\Fixtures\Api\MockMethod;
use RuntimeException;

class FieldMapParameterHandlerTest extends TestCase
{
    private RequestBuilder $requestBuilder;

    private ReflectionMethod $reflectionMethod;

    protected function setUp(): void
    {
        parent::setUp();
        $this->requestBuilder = new RequestBuilder(new Uri('https://example.com'), new GET('/users'));
        $this->reflectionMethod = new ReflectionMethod(MockMethod::class, 'mockMethod');
    }

    #[Test]
    public function shouldThrowExceptionWhenValueIsNull(): void
    {
        // given
        $fieldMapParameterHandler = new FieldMapParameterHandler(false, BuiltInConverters::ToStringConverter(), $this->reflectionMethod, 0);

        // when
        CatchException::when($fieldMapParameterHandler)->apply($this->requestBuilder, null);

        // then
        CatchException::assertThat()
            ->isInstanceOf(RuntimeException::class)
            ->hasMessage('Method MockMethod::mockMethod() parameter #1. Field map was null.');
    }

    #[Test]
    public function shouldThrowExceptionWhenValueIsNotArray(): void
    {
        // given
        $fieldMapParameterHandler = new FieldMapParameterHandler(false, BuiltInConverters::ToStringConverter(), $this->reflectionMethod, 0);

        // when
        CatchException::when($fieldMapParameterHandler)->apply($this->requestBuilder, 'some-string-value');

        // then
        CatchException::assertThat()
            ->isInstanceOf(RuntimeException::class)
            ->hasMessage('Method MockMethod::mockMethod() parameter #1. Parameter should be an array.');
    }

    #[Test]
    public function shouldThrowExceptionWhenKeyInArrayIsNull(): void
    {
        // given
        $fieldMapParameterHandler = new FieldMapParameterHandler(false, BuiltInConverters::ToStringConverter(), $this->reflectionMethod, 0);

        // when
        CatchException::when($fieldMapParameterHandler)->apply($this->requestBuilder, [null => 'value']);

        // then
        CatchException::assertThat()
            ->isInstanceOf(RuntimeException::class)
            ->hasMessage('Method MockMethod::mockMethod() parameter #1. Field map contained empty key.');
    }

    #[Test]
    public function shouldThrowExceptionWhenValueInArrayIsNull(): void
    {
        // given
        $fieldMapParameterHandler = new FieldMapParameterHandler(false, BuiltInConverters::ToStringConverter(), $this->reflectionMethod, 0);

        // when
        CatchException::when($fieldMapParameterHandler)->apply($this->requestBuilder, ['key' => null]);

        // then
        CatchException::assertThat()
            ->isInstanceOf(RuntimeException::class)
            ->hasMessage("Method MockMethod::mockMethod() parameter #1. Field map contained null value for key 'key'.");
    }

    #[Test]
    public function shouldAddFormFields(): void
    {
        // given
        $fieldMapParameterHandler = new FieldMapParameterHandler(false, BuiltInConverters::ToStringConverter(), $this->reflectionMethod, 0);

        // when
        $fieldMapParameterHandler->apply($this->requestBuilder, ['x-custom' => 'jon+doe', 'x-age' => 34]);

        // then
        $request = $this->requestBuilder->build();
        $this->assertSame('x-custom=jon%2Bdoe&x-age=34', $request->getBody()->getContents());
    }
}
