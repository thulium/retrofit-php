<?php
declare(strict_types=1);

namespace Retrofit\Tests\Internal\ParameterHandler\Factory;

use Ouzo\Tests\CatchException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use Retrofit\Attribute\Path;
use Retrofit\Attribute\POST;
use Retrofit\Internal\BuiltInConverterFactory;
use Retrofit\Internal\ConverterProvider;
use Retrofit\Internal\ParameterHandler\Factory\PathParameterHandlerFactory;
use Retrofit\Internal\ParameterHandler\PathParameterHandler;
use Retrofit\Tests\Fixtures\Api\FullyValidApi;
use RuntimeException;

class PathParameterHandlerFactoryTest extends TestCase
{
    private ReflectionMethod $reflectionMethod;
    private ConverterProvider $converterProvider;

    public function setUp(): void
    {
        parent::setUp();
        $this->reflectionMethod = new ReflectionMethod(FullyValidApi::class, 'createUser');
        $this->converterProvider = new ConverterProvider([new BuiltInConverterFactory()]);
    }

    #[Test]
    public function shouldThrowExceptionWhenPathNameDoesNotPresentInUrl(): void
    {
        //given
        $pathParameterHandlerFactory = new PathParameterHandlerFactory($this->converterProvider);

        //when
        CatchException::when($pathParameterHandlerFactory)->create(new Path('not-matching'), new POST('/users/{login}'), $this->reflectionMethod, 0);

        //then
        CatchException::assertThat()
            ->isInstanceOf(RuntimeException::class)
            ->hasMessage("Method FullyValidApi::createUser() parameter #1. URL '/users/{login}' does not contain 'not-matching'.");
    }

    #[Test]
    public function shouldCreatePathParameterHandler(): void
    {
        //given
        $pathParameterHandlerFactory = new PathParameterHandlerFactory($this->converterProvider);

        //when
        $parameterHandler = $pathParameterHandlerFactory->create(new Path('login'), new POST('/users/{login}'), $this->reflectionMethod, 0);

        //then
        $this->assertInstanceOf(PathParameterHandler::class, $parameterHandler);
    }
}
