<?php

declare(strict_types=1);

namespace Retrofit\Tests\Core\Internal\ParameterHandler\Factory;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use Retrofit\Core\Attribute\GET;
use Retrofit\Core\Attribute\Header;
use Retrofit\Core\Internal\BuiltInConverterFactory;
use Retrofit\Core\Internal\ConverterProvider;
use Retrofit\Core\Internal\ParameterHandler\Factory\HeaderParameterHandlerFactory;
use Retrofit\Core\Internal\ParameterHandler\HeaderParameterHandler;
use Retrofit\Core\Type;
use Retrofit\Tests\Fixtures\Api\MockMethod;

class HeaderParameterHandlerFactoryTest extends TestCase
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
    public function shouldCreateHeaderParameterHandler(): void
    {
        // given
        $headerParameterHandlerFactory = new HeaderParameterHandlerFactory($this->converterProvider);

        // when
        $parameterHandler = $headerParameterHandlerFactory->create(
            new Header('x-custom'),
            new GET('/users/{login}'),
            null,
            $this->reflectionMethod,
            1,
            new Type('string'),
        );

        // then
        $this->assertInstanceOf(HeaderParameterHandler::class, $parameterHandler);
    }
}
