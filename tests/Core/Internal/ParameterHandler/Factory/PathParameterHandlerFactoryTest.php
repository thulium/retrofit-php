<?php

declare(strict_types=1);

namespace Retrofit\Tests\Core\Internal\ParameterHandler\Factory;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use Retrofit\Core\Attribute\Path;
use Retrofit\Core\Attribute\POST;
use Retrofit\Core\Internal\BuiltInConverterFactory;
use Retrofit\Core\Internal\ConverterProvider;
use Retrofit\Core\Internal\ParameterHandler\Factory\PathParameterHandlerFactory;
use Retrofit\Core\Internal\ParameterHandler\PathParameterHandler;
use Retrofit\Core\Type;
use Retrofit\Tests\Fixtures\Api\MockMethod;

class PathParameterHandlerFactoryTest extends TestCase
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
    public function shouldCreatePathParameterHandler(): void
    {
        //given
        $pathParameterHandlerFactory = new PathParameterHandlerFactory($this->converterProvider);

        //when
        $parameterHandler = $pathParameterHandlerFactory->create(
            new Path('login'),
            new POST('/users/{login}'),
            null,
            $this->reflectionMethod,
            0,
            new Type('string'),
        );

        //then
        $this->assertInstanceOf(PathParameterHandler::class, $parameterHandler);
    }
}
