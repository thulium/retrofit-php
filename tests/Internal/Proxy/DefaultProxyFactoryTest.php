<?php
declare(strict_types=1);

namespace Retrofit\Tests\Internal\Proxy;

use Nyholm\Psr7\Uri;
use Ouzo\Tests\Assert;
use Ouzo\Tests\CatchException;
use Ouzo\Tests\Mock\Mock;
use Ouzo\Tests\Mock\MockInterface;
use PhpParser\BuilderFactory;
use PhpParser\PrettyPrinter\Standard;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;
use Retrofit\Attribute\Body;
use Retrofit\Attribute\GET;
use Retrofit\Attribute\Path;
use Retrofit\Attribute\POST;
use Retrofit\HttpClient;
use Retrofit\Internal\BuiltInConverterFactory;
use Retrofit\Internal\ConverterProvider;
use Retrofit\Internal\Proxy\DefaultProxyFactory;
use Retrofit\Internal\Proxy\ProxyFactory;
use Retrofit\Retrofit;
use Retrofit\Tests\Fixtures\Api\FullyValidApi;
use Retrofit\Tests\Fixtures\Api\MethodWithoutReturnType;
use Retrofit\Tests\Fixtures\Api\MethodWithWrongReturnType;
use Retrofit\Tests\Fixtures\Api\NullableParameter;
use Retrofit\Tests\Fixtures\Api\ParameterWithoutType;
use Retrofit\Tests\Fixtures\Model\UserRequest;
use RuntimeException;

#[RunTestsInSeparateProcesses]
class DefaultProxyFactoryTest extends TestCase
{
    private ProxyFactory $defaultProxyFactory;
    private Retrofit $retrofit;

    public function setUp(): void
    {
        parent::setUp();
        /** @var HttpClient|MockInterface $httpClient */
        $httpClient = Mock::create(HttpClient::class);

        $builderFactory = new BuilderFactory();
        $prettyPrinterAbstract = new Standard();
        $this->defaultProxyFactory = new DefaultProxyFactory($builderFactory, $prettyPrinterAbstract);
        $this->retrofit = new Retrofit($httpClient, new Uri(), new ConverterProvider([new BuiltInConverterFactory()]), $this->defaultProxyFactory);
    }

    #[Test]
    public function shouldCreateImplementationWithImplSuffixInName(): void
    {
        //given
        $service = new ReflectionClass(FullyValidApi::class);

        //when
        $impl = $this->defaultProxyFactory->create($this->retrofit, $service);

        //then
        $reflectionClass = new ReflectionClass($impl);
        $this->assertSame('Retrofit\Proxy\Retrofit\Tests\Fixtures\Api\FullyValidApiImpl', $reflectionClass->getName());
        $this->assertSame([FullyValidApi::class], $reflectionClass->getInterfaceNames());
    }

    #[Test]
    public function shouldCreateImplementationWithOneConstructorParameterWhichIsRetrofit(): void
    {
        //given
        $service = new ReflectionClass(FullyValidApi::class);

        //when
        $impl = $this->defaultProxyFactory->create($this->retrofit, $service);

        //then
        $reflectionClass = new ReflectionClass($impl);
        $reflectionMethod = $reflectionClass->getConstructor();
        $reflectionParameters = $reflectionMethod->getParameters();
        $this->assertCount(1, $reflectionParameters);

        $reflectionParameter = $reflectionParameters[0];
        $this->assertSame(Retrofit::class, $reflectionParameter->getType()->getName());
        $this->assertSame('retrofit', $reflectionParameter->getName());
        $this->assertFalse($reflectionParameter->isPromoted());
        $this->assertFalse($reflectionParameter->allowsNull());
    }

    #[Test]
    public function shouldCreateImplementationWithMethods(): void
    {
        //given
        $service = new ReflectionClass(FullyValidApi::class);

        //when
        $impl = $this->defaultProxyFactory->create($this->retrofit, $service);

        //then
        $reflectionClass = new ReflectionClass($impl);
        $this->assertTrue($reflectionClass->hasMethod('getInfo'));
        $this->assertTrue($reflectionClass->hasMethod('createUser'));

        $reflectionMethod1 = $reflectionClass->getMethod('getInfo');
        $reflectionAttributes1 = $reflectionMethod1->getAttributes();
        Assert::thatArray($reflectionAttributes1)
            ->extracting(fn(ReflectionAttribute $a): string => $a->getName(), fn(ReflectionAttribute $a): array => $a->getArguments())
            ->containsExactly(
                [GET::class, ['/info/{login}']]
            );

        $reflectionMethod2 = $reflectionClass->getMethod('createUser');
        $reflectionAttributes2 = $reflectionMethod2->getAttributes();
        Assert::thatArray($reflectionAttributes2)
            ->extracting(fn(ReflectionAttribute $a): string => $a->getName(), fn(ReflectionAttribute $a): array => $a->getArguments())
            ->containsExactly(
                [POST::class, ['/users/{login}']]
            );
    }

    #[Test]
    public function shouldThrowExceptionWhenMethodDoesNotHaveReturnType(): void
    {
        //given
        $service = new ReflectionClass(MethodWithoutReturnType::class);

        //when
        CatchException::when($this->defaultProxyFactory)->create($this->retrofit, $service);

        //then
        CatchException::assertThat()
            ->isInstanceOf(RuntimeException::class)
            ->hasMessage('Method MethodWithoutReturnType::withoutReturnType(). Method return type is required, none found.');
    }

    #[Test]
    public function shouldThrowExceptionWhenMethodDoesNotHaveCallReturnType(): void
    {
        //given
        $service = new ReflectionClass(MethodWithWrongReturnType::class);

        //when
        CatchException::when($this->defaultProxyFactory)->create($this->retrofit, $service);

        //then
        CatchException::assertThat()
            ->isInstanceOf(RuntimeException::class)
            ->hasMessage("Method MethodWithWrongReturnType::methodWithWrongReturnType(). Method return type should be a Retrofit\Call class. 'int' return type found.");
    }

    #[Test]
    public function shouldCreateMethodWithParametersContainsAttributes(): void
    {
        //given
        $service = new ReflectionClass(FullyValidApi::class);

        //when
        $impl = $this->defaultProxyFactory->create($this->retrofit, $service);

        //then
        $reflectionMethod = new ReflectionMethod($impl, 'createUser');
        $reflectionParameters = $reflectionMethod->getParameters();

        Assert::thatArray($reflectionParameters)
            ->extracting(
                fn(ReflectionParameter $p): string => $p->getName(),
                fn(ReflectionParameter $p): array => collect($p->getAttributes())->map(fn(ReflectionAttribute $a) => $a->getName())->all(),
                fn(ReflectionParameter $p): string => $p->getType()->getName()
            )
            ->containsOnly(
                ['login', [Path::class], 'string'],
                ['userRequest', [Body::class], UserRequest::class]
            );
    }

    #[Test]
    public function shouldThrowExceptionWhenParametersDoesNotHaveType(): void
    {
        //given
        $service = new ReflectionClass(ParameterWithoutType::class);

        //when
        CatchException::when($this->defaultProxyFactory)->create($this->retrofit, $service);

        //then
        CatchException::assertThat()
            ->isInstanceOf(RuntimeException::class)
            ->hasMessage('Method ParameterWithoutType::parameterWithoutType() parameter #1. Parameter type is required, none found.');
    }

    #[Test]
    public function shouldHandleNullableParameterType(): void
    {
        //given
        $service = new ReflectionClass(NullableParameter::class);

        //when
        $impl = $this->defaultProxyFactory->create($this->retrofit, $service);

        //then
        $reflectionMethod = new ReflectionMethod($impl, 'nullableParameter');
        $reflectionParameters = $reflectionMethod->getParameters();

        Assert::thatArray($reflectionParameters)
            ->extracting(fn(ReflectionParameter $p): bool => $p->getType()->allowsNull())
            ->containsOnly(true);
    }
}
