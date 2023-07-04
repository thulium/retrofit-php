<?php
declare(strict_types=1);

namespace Retrofit\Tests\Internal\ParameterHandler\Factory;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use Retrofit\Attribute\GET;
use Retrofit\Attribute\QueryName;
use Retrofit\Internal\BuiltInConverterFactory;
use Retrofit\Internal\ConverterProvider;
use Retrofit\Internal\ParameterHandler\Factory\QueryNameParameterHandlerFactory;
use Retrofit\Internal\ParameterHandler\QueryNameParameterHandler;
use Retrofit\Tests\Fixtures\Api\MockMethod;

class QueryNameParameterHandlerFactoryTest extends TestCase
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
    public function shouldCreateQueryNameParameterHandler(): void
    {
        //given
        $queryNameParameterHandlerFactory = new QueryNameParameterHandlerFactory($this->converterProvider);

        //when
        $parameterHandler = $queryNameParameterHandlerFactory->create(new QueryName(), new GET('/users'), $this->reflectionMethod, 1);

        //then
        $this->assertInstanceOf(QueryNameParameterHandler::class, $parameterHandler);
    }
}
