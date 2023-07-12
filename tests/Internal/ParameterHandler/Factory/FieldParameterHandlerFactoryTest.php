<?php
declare(strict_types=1);

namespace Retrofit\Tests\Internal\ParameterHandler\Factory;

use Ouzo\Tests\CatchException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use Retrofit\Attribute\Field;
use Retrofit\Attribute\GET;
use Retrofit\Internal\BuiltInConverterFactory;
use Retrofit\Internal\ConverterProvider;
use Retrofit\Internal\Encoding;
use Retrofit\Internal\ParameterHandler\Factory\FieldParameterHandlerFactory;
use Retrofit\Internal\ParameterHandler\FieldParameterHandler;
use Retrofit\Tests\Fixtures\Api\MockMethod;
use Retrofit\Type;
use RuntimeException;

class FieldParameterHandlerFactoryTest extends TestCase
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
    #[TestWith([Encoding::MULTIPART])]
    #[TestWith([null])]
    public function shouldThrowExceptionWhenMetodEncodedIsNotFormUrlEncoded(?Encoding $encoding): void
    {
        //given
        $fieldParameterHandlerFactory = new FieldParameterHandlerFactory($this->converterProvider);

        //when
        CatchException::when($fieldParameterHandlerFactory)
            ->create(new Field('name'), new GET('/users/{login}'), $encoding, $this->reflectionMethod, 1, new Type('string'));

        //then
        CatchException::assertThat()
            ->isInstanceOf(RuntimeException::class)
            ->hasMessage('Method MockMethod::mockMethod() parameter #2. #[Field] parameters can only be used with form encoding.');
    }

    #[Test]
    public function shouldCreateFieldParameterHandler(): void
    {
        //given
        $fieldParameterHandlerFactory = new FieldParameterHandlerFactory($this->converterProvider);

        //when
        $parameterHandler = $fieldParameterHandlerFactory->create(
            new Field('name'), new GET('/users/{login}'), Encoding::FORM_URL_ENCODED, $this->reflectionMethod, 1, new Type('string')
        );

        //then
        $this->assertInstanceOf(FieldParameterHandler::class, $parameterHandler);
    }
}
