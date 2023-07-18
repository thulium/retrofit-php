<?php

declare(strict_types=1);

namespace Retrofit\Tests\Core\Internal\ParameterHandler\Factory;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use Retrofit\Core\Attribute\Body;
use Retrofit\Core\Attribute\GET;
use Retrofit\Core\Internal\BuiltInConverterFactory;
use Retrofit\Core\Internal\ConverterProvider;
use Retrofit\Core\Internal\ParameterHandler\BodyParameterHandler;
use Retrofit\Core\Internal\ParameterHandler\Factory\BodyParameterHandlerFactory;
use Retrofit\Core\Type;
use Retrofit\Tests\Fixtures\Api\MockMethod;
use Retrofit\Tests\Fixtures\Converter\TestConverterFactory;

class BodyParameterHandlerFactoryTest extends TestCase
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
    public function shouldCreateBodyParameterHandler(): void
    {
        //given
        $partParameterHandlerFactory = new BodyParameterHandlerFactory($this->converterProvider);

        //when
        $parameterHandler = $partParameterHandlerFactory->create(
            new Body(),
            new GET('/users/{login}'),
            null,
            $this->reflectionMethod,
            1,
            new Type('string'),
        );

        //then
        $this->assertInstanceOf(BodyParameterHandler::class, $parameterHandler);
    }
}
