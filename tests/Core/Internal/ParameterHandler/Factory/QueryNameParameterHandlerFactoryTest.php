<?php
declare(strict_types=1);

namespace Retrofit\Tests\Core\Internal\ParameterHandler\Factory;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use Retrofit\Core\Attribute\GET;
use Retrofit\Core\Attribute\QueryName;
use Retrofit\Core\Internal\BuiltInConverterFactory;
use Retrofit\Core\Internal\ConverterProvider;
use Retrofit\Core\Internal\ParameterHandler\Factory\QueryNameParameterHandlerFactory;
use Retrofit\Core\Internal\ParameterHandler\QueryNameParameterHandler;
use Retrofit\Core\Type;
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
        $parameterHandler = $queryNameParameterHandlerFactory->create(
            new QueryName(), new GET('/users'), null, $this->reflectionMethod, 1, new Type('string')
        );

        //then
        $this->assertInstanceOf(QueryNameParameterHandler::class, $parameterHandler);
    }
}
