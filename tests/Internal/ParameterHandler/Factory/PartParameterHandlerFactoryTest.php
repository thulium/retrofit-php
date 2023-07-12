<?php
declare(strict_types=1);

namespace Retrofit\Tests\Internal\ParameterHandler\Factory;

use Ouzo\Tests\CatchException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use Retrofit\Attribute\GET;
use Retrofit\Attribute\Part;
use Retrofit\Internal\BuiltInConverterFactory;
use Retrofit\Internal\ConverterProvider;
use Retrofit\Internal\Encoding;
use Retrofit\Internal\ParameterHandler\Factory\PartParameterHandlerFactory;
use Retrofit\Internal\ParameterHandler\PartParameterHandler;
use Retrofit\Tests\Fixtures\Api\MockMethod;
use Retrofit\Type;
use RuntimeException;

class PartParameterHandlerFactoryTest extends TestCase
{
    private ReflectionMethod $reflectionMethod;
    private ConverterProvider $converterProvider;

    public function setUp(): void
    {
        parent::setUp();
        $this->reflectionMethod = new ReflectionMethod(MockMethod::class, 'mockMethod');
        $this->converterProvider = new ConverterProvider([new BuiltInConverterFactory()]);
    }

    #[Test]
    #[TestWith([Encoding::FORM_URL_ENCODED])]
    #[TestWith([null])]
    public function shouldThrowExceptionWhenMetodEncodedIsNotMultipart(?Encoding $encoding): void
    {
        //given
        $partParameterHandlerFactory = new PartParameterHandlerFactory($this->converterProvider);

        //when
        CatchException::when($partParameterHandlerFactory)
            ->create(new Part('name'), new GET('/users/{login}'), $encoding, $this->reflectionMethod, 1, new Type('string'));

        //then
        CatchException::assertThat()
            ->isInstanceOf(RuntimeException::class)
            ->hasMessage('Method MockMethod::mockMethod() parameter #2. #[Part] parameters can only be used with multipart.');
    }

    #[Test]
    public function shouldCreatePartParameterHandler(): void
    {
        //given
        $partParameterHandlerFactory = new PartParameterHandlerFactory($this->converterProvider);

        //when
        $parameterHandler = $partParameterHandlerFactory->create(
            new Part('name'), new GET('/users/{login}'), Encoding::MULTIPART, $this->reflectionMethod, 1, new Type('string')
        );

        //then
        $this->assertInstanceOf(PartParameterHandler::class, $parameterHandler);
    }
}
