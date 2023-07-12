<?php
declare(strict_types=1);

namespace Retrofit\Tests\Internal\ParameterHandler\Factory;

use Ouzo\Tests\CatchException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use Retrofit\Attribute\GET;
use Retrofit\Attribute\PartMap;
use Retrofit\Internal\BuiltInConverterFactory;
use Retrofit\Internal\ConverterProvider;
use Retrofit\Internal\Encoding;
use Retrofit\Internal\ParameterHandler\Factory\PartMapParameterHandlerFactory;
use Retrofit\Internal\ParameterHandler\PartMapParameterHandler;
use Retrofit\Tests\Fixtures\Api\MockMethod;
use Retrofit\Type;
use RuntimeException;

class PartMapParameterHandlerFactoryTest extends TestCase
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
        $partMapParameterHandlerFactory = new PartMapParameterHandlerFactory($this->converterProvider);

        //when
        CatchException::when($partMapParameterHandlerFactory)
            ->create(new PartMap(), new GET('/users/{login}'), $encoding, $this->reflectionMethod, 1, new Type('string'));

        //then
        CatchException::assertThat()
            ->isInstanceOf(RuntimeException::class)
            ->hasMessage('Method MockMethod::mockMethod() parameter #2. #[PartMap] parameters can only be used with multipart.');
    }

    #[Test]
    public function shouldCreatePartMapParameterHandler(): void
    {
        //given
        $partMapParameterHandlerFactory = new PartMapParameterHandlerFactory($this->converterProvider);

        //when
        $parameterHandler = $partMapParameterHandlerFactory->create(
            new PartMap(), new GET('/users/{login}'), Encoding::MULTIPART, $this->reflectionMethod, 1, new Type('string')
        );

        //then
        $this->assertInstanceOf(PartMapParameterHandler::class, $parameterHandler);
    }
}
