<?php
declare(strict_types=1);

namespace Retrofit\Tests\Internal\ParameterHandler\Factory;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use Retrofit\Attribute\Body;
use Retrofit\Attribute\GET;
use Retrofit\Internal\BuiltInConverterFactory;
use Retrofit\Internal\ConverterProvider;
use Retrofit\Internal\ParameterHandler\BodyParameterHandler;
use Retrofit\Internal\ParameterHandler\Factory\BodyParameterHandlerFactory;
use Retrofit\Tests\Fixtures\Api\MockMethod;
use Retrofit\Type;

class BodyParameterHandlerFactoryTest extends TestCase
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
    public function shouldCreateBodyParameterHandler(): void
    {
        //given
        $partParameterHandlerFactory = new BodyParameterHandlerFactory($this->converterProvider);

        //when
        $parameterHandler = $partParameterHandlerFactory->create(
            new Body(), new GET('/users/{login}'), null, $this->reflectionMethod, 1, new Type('string')
        );

        //then
        $this->assertInstanceOf(BodyParameterHandler::class, $parameterHandler);
    }
}
