<?php
declare(strict_types=1);

namespace Retrofit\Tests\Internal\Proxy;

use Nyholm\Psr7\Uri;
use Ouzo\Tests\Assert;
use Ouzo\Tests\Mock\Mock;
use Ouzo\Tests\Mock\MockInterface;
use PhpParser\BuilderFactory;
use PhpParser\PrettyPrinter\Standard;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionAttribute;
use ReflectionClass;
use Retrofit\Attribute\GET;
use Retrofit\Attribute\POST;
use Retrofit\HttpClient;
use Retrofit\Internal\BuiltInConverters;
use Retrofit\Internal\ConverterProvider;
use Retrofit\Internal\Proxy\DefaultProxyFactory;
use Retrofit\Internal\Proxy\ProxyFactory;
use Retrofit\Retrofit;
use Retrofit\Tests\Fixtures\Api\ValidApi;

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
        $this->retrofit = new Retrofit($httpClient, new Uri(), new ConverterProvider([new BuiltInConverters()]), $this->defaultProxyFactory);
    }

    #[Test]
    public function shouldCreateImplementationWithImplSuffixInName(): void
    {
        //given
        $service = new ReflectionClass(ValidApi::class);

        //when
        $validApiImpl = $this->defaultProxyFactory->create($this->retrofit, $service);

        //then
        $reflectionClass = new ReflectionClass($validApiImpl);
        $this->assertSame('Retrofit\Proxy\Retrofit\Tests\Fixtures\Api\ValidApiImpl', $reflectionClass->getName());
        $this->assertSame([ValidApi::class], $reflectionClass->getInterfaceNames());
    }

    #[Test]
    public function shouldCreateImplementationWithOneConstructorParameterWhichIsRetrofit(): void
    {
        //given
        $service = new ReflectionClass(ValidApi::class);

        //when
        $validApiImpl = $this->defaultProxyFactory->create($this->retrofit, $service);

        //then
        $reflectionClass = new ReflectionClass($validApiImpl);
        $reflectionMethod = $reflectionClass->getConstructor();
        $reflectionParameters = $reflectionMethod->getParameters();
        $this->assertCount(1, $reflectionParameters);

        $reflectionParameter = $reflectionParameters[0];
        $this->assertSame(Retrofit::class, $reflectionParameter->getType()->getName());
        $this->assertSame('retrofit', $reflectionParameter->getName());
        $this->assertTrue($reflectionParameter->isPromoted());
        $this->assertFalse($reflectionParameter->allowsNull());
    }

    #[Test]
    public function shouldCreateImplementationWithMethods(): void
    {
        //given
        $service = new ReflectionClass(ValidApi::class);

        //when
        $validApiImpl = $this->defaultProxyFactory->create($this->retrofit, $service);

        //then
        $reflectionClass = new ReflectionClass($validApiImpl);
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
                [POST::class, ['/users']]
            );
    }
}
