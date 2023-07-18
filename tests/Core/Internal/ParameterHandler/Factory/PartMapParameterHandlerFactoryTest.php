<?php

declare(strict_types=1);

namespace Retrofit\Tests\Core\Internal\ParameterHandler\Factory;

use Ouzo\Tests\CatchException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use Retrofit\Core\Attribute\GET;
use Retrofit\Core\Attribute\PartMap;
use Retrofit\Core\Internal\BuiltInConverterFactory;
use Retrofit\Core\Internal\ConverterProvider;
use Retrofit\Core\Internal\Encoding;
use Retrofit\Core\Internal\ParameterHandler\Factory\PartMapParameterHandlerFactory;
use Retrofit\Core\Internal\ParameterHandler\PartMapParameterHandler;
use Retrofit\Core\Type;
use Retrofit\Tests\Fixtures\Api\MockMethod;
use Retrofit\Tests\Fixtures\Converter\TestConverterFactory;
use RuntimeException;

class PartMapParameterHandlerFactoryTest extends TestCase
{
    private ReflectionMethod $reflectionMethod;

    private ConverterProvider $converterProvider;

    public function setUp(): void
    {
        parent::setUp();
        $this->reflectionMethod = new ReflectionMethod(MockMethod::class, 'mockMethod');
        $this->converterProvider = new ConverterProvider([new BuiltInConverterFactory(), new TestConverterFactory()]);
    }

    #[Test]
    #[TestWith([Encoding::FORM_URL_ENCODED])]
    #[TestWith([null])]
    public function shouldThrowExceptionWhenMetodEncodedIsNotMultipart(?Encoding $encoding): void
    {
        // given
        $partMapParameterHandlerFactory = new PartMapParameterHandlerFactory($this->converterProvider);

        // when
        CatchException::when($partMapParameterHandlerFactory)
            ->create(new PartMap(), new GET('/users/{login}'), $encoding, $this->reflectionMethod, 1, new Type('string'));

        // then
        CatchException::assertThat()
            ->isInstanceOf(RuntimeException::class)
            ->hasMessage('Method MockMethod::mockMethod() parameter #2. #[PartMap] parameters can only be used with multipart.');
    }

    #[Test]
    public function shouldCreatePartMapParameterHandler(): void
    {
        // given
        $partMapParameterHandlerFactory = new PartMapParameterHandlerFactory($this->converterProvider);

        // when
        $parameterHandler = $partMapParameterHandlerFactory->create(
            new PartMap(),
            new GET('/users/{login}'),
            Encoding::MULTIPART,
            $this->reflectionMethod,
            1,
            new Type('string'),
        );

        // then
        $this->assertInstanceOf(PartMapParameterHandler::class, $parameterHandler);
    }
}
