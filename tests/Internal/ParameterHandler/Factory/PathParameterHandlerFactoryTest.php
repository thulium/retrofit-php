<?php
declare(strict_types=1);

namespace Retrofit\Tests\Internal\ParameterHandler\Factory;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use Retrofit\Attribute\Path;
use Retrofit\Attribute\POST;
use Retrofit\Internal\BuiltInConverterFactory;
use Retrofit\Internal\ConverterProvider;
use Retrofit\Internal\ParameterHandler\Factory\PathParameterHandlerFactory;
use Retrofit\Internal\ParameterHandler\PathParameterHandler;
use Retrofit\Tests\Fixtures\Api\MockMethod;
use Retrofit\Type;

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
            new Path('login'), new POST('/users/{login}'), null, $this->reflectionMethod, 0, new Type('string')
        );

        //then
        $this->assertInstanceOf(PathParameterHandler::class, $parameterHandler);
    }
}
