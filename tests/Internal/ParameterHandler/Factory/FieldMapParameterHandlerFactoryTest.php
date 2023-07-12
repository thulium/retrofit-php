<?php
declare(strict_types=1);

namespace Retrofit\Tests\Internal\ParameterHandler\Factory;

use Ouzo\Tests\CatchException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use Retrofit\Attribute\FieldMap;
use Retrofit\Attribute\GET;
use Retrofit\Internal\BuiltInConverterFactory;
use Retrofit\Internal\ConverterProvider;
use Retrofit\Internal\Encoding;
use Retrofit\Internal\ParameterHandler\Factory\FieldMapParameterHandlerFactory;
use Retrofit\Internal\ParameterHandler\FieldMapParameterHandler;
use Retrofit\Tests\Fixtures\Api\MockMethod;
use Retrofit\Type;
use RuntimeException;

class FieldMapParameterHandlerFactoryTest extends TestCase
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
        $fieldMapParameterHandlerFactory = new FieldMapParameterHandlerFactory($this->converterProvider);

        //when
        CatchException::when($fieldMapParameterHandlerFactory)->create(
            new FieldMap(false), new GET('/users/{login}'), $encoding, $this->reflectionMethod, 1, new Type('string')
        );

        //then
        CatchException::assertThat()
            ->isInstanceOf(RuntimeException::class)
            ->hasMessage('Method MockMethod::mockMethod() parameter #2. #[FieldMap] parameters can only be used with form encoding.');
    }

    #[Test]
    public function shouldCreateFieldMapParameterHandler(): void
    {
        //given
        $fieldMapParameterHandlerFactory = new FieldMapParameterHandlerFactory($this->converterProvider);

        //when
        $parameterHandler = $fieldMapParameterHandlerFactory->create(
            new FieldMap(false), new GET('/users/{login}'), Encoding::FORM_URL_ENCODED, $this->reflectionMethod, 1, new Type('string')
        );

        //then
        $this->assertInstanceOf(FieldMapParameterHandler::class, $parameterHandler);
    }
}
