<?php
declare(strict_types=1);

namespace Retrofit\Tests\Internal\ParameterHandler\Factory;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use Retrofit\Attribute\GET;
use Retrofit\Attribute\Path;
use Retrofit\Internal\BuiltInConverters;
use Retrofit\Internal\ConverterProvider;
use Retrofit\Internal\ParameterHandler\Factory\PathParameterHandlerFactory;
use Retrofit\Internal\ParameterHandler\PathParameterHandler;
use Retrofit\Tests\Fixtures\SomeApi;
use RuntimeException;

class PathParameterHandlerFactoryTest extends TestCase
{
    private ReflectionMethod $reflectionMethod;

    public function setUp(): void
    {
        parent::setUp();
        $this->reflectionMethod = new ReflectionMethod(SomeApi::class, 'getUserByName');
    }

    #[Test]
    public function shouldThrowExceptionWhenPathNameDoesNotPresentInUrl(): void
    {
        //given
        $pathParameterHandlerFactory = new PathParameterHandlerFactory(new GET('/user/{name}'), new ConverterProvider([new BuiltInConverters()]));

        //when
        try {
            $pathParameterHandlerFactory->create(new Path('not-matching'), $this->reflectionMethod, 0);
            //then
        } catch (RuntimeException $e) {
            $this->assertSame("Method SomeApi::getUserByName() parameter #1. URL '/user/{name}' does not contain 'not-matching'.", $e->getMessage());
        }
    }

    #[Test]
    public function shouldCreatePathParameterHandler(): void
    {
        //given
        $pathParameterHandlerFactory = new PathParameterHandlerFactory(new GET('/user/{name}'), new ConverterProvider([new BuiltInConverters()]));

        //when
        $parameterHandler = $pathParameterHandlerFactory->create(new Path('name'), $this->reflectionMethod, 0);

        //then
        $this->assertInstanceOf(PathParameterHandler::class, $parameterHandler);
    }
}
