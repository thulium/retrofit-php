<?php
declare(strict_types=1);

namespace Retrofit\Internal\Proxy;

use PhpParser\Builder\Class_;
use PhpParser\Builder\Method;
use PhpParser\Builder\Namespace_;
use PhpParser\Builder\Param;
use PhpParser\BuilderFactory;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name;
use PhpParser\Node\NullableType;
use PhpParser\Node\Scalar\MagicConst\Function_;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Return_;
use PhpParser\PrettyPrinterAbstract;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionMethod;
use Retrofit\Call;
use Retrofit\Internal\ServiceMethod;
use Retrofit\Internal\ServiceMethodFactory;
use Retrofit\Internal\Utils\Utils;
use Retrofit\Retrofit;

/**
 * Creates an proxy which implements all of the methods from the service interface.
 *
 * In the constructor of an interface implementation a {@link Retrofit} object is injected. Each of the implemented methods
 * calls {@link ServiceMethodFactory::create()} method to create a {@link ServiceMethod} implementation with required
 * details - parsed and validated attributes. This created method is immediately invoked with passed all arguments.
 *
 * Example (pseudo-code):
 * <pre>
 * namespace Retrofit\Proxy\Retrofit\Tests\Fixtures;
 *
 * class SomeApiImpl implements \Retrofit\Tests\Fixtures\SomeApi
 * {
 *      public function __construct(private \Retrofit\Retrofit $retrofit)
 *      {
 *      }
 *
 *      #[\Retrofit\Attribute\GET('/users/{id}')]
 *      public function getUser(#[\Retrofit\Attribute\Path('id')] int $id): \Retrofit\Call
 *      {
 *          return \Retrofit\Internal\ServiceMethodFactory::create($this->retrofit, '\\Retrofit\\Tests\\Fixtures\\SomeApi', __FUNCTION__)->invoke(func_get_args());
 *      }
 * }
 * </pre>
 */
readonly class DefaultProxyFactory implements ProxyFactory
{
    private const SERVICE_IMPLEMENTATION_NAMESPACE_PREFIX = 'Retrofit\Proxy\\';
    private const SERVICE_IMPLEMENTATION_CLASS_SUFFIX = 'Impl';

    public function __construct(
        private BuilderFactory $builderFactory,
        private PrettyPrinterAbstract $prettyPrinterAbstract
    )
    {
    }

    public function create(Retrofit $retrofit, ReflectionClass $service): object
    {
        $proxyServiceNamespace = self::SERVICE_IMPLEMENTATION_NAMESPACE_PREFIX . $service->getNamespaceName();
        $proxyServiceClassName = $service->getShortName() . self::SERVICE_IMPLEMENTATION_CLASS_SUFFIX;

        $serviceClassImplementation = $this->serviceClassImplementation($service, $proxyServiceClassName);
        $this->appendConstructor($serviceClassImplementation);
        $this->appendMethods($service, $serviceClassImplementation);
        $serviceClassImplementationInNamespace = $this->wrapInNamespace($proxyServiceNamespace, $serviceClassImplementation);

        $proxyServiceClass = $this->prettyPrinterAbstract->prettyPrint([$serviceClassImplementationInNamespace->getNode()]);

        eval($proxyServiceClass);

        $proxyServiceFQCN = Utils::toFQCN($proxyServiceNamespace, $proxyServiceClassName);
        return new $proxyServiceFQCN($retrofit);
    }

    private function serviceClassImplementation(ReflectionClass $service, string $proxyServiceName): Class_
    {
        $serviceFQCN = Utils::toFQCN($service->getName());
        return $this->builderFactory
            ->class($proxyServiceName)
            ->implement($serviceFQCN);
    }

    private function appendConstructor(Class_ $serviceClassImplementation): void
    {
        $retrofitParameter = $this->builderFactory
            ->param('retrofit')
            ->makePrivate()
            ->setType(Utils::toFQCN(Retrofit::class));
        $constructor = $this->builderFactory
            ->method('__construct')
            ->makePublic()
            ->addParam($retrofitParameter->getNode());
        $serviceClassImplementation->addStmt($constructor->getNode());
    }

    private function appendMethods(ReflectionClass $service, Class_ $serviceClassImplementation): void
    {
        $serviceMethodInvokeReturnStmt = $this->createServiceMethodInvokeReturnStmt($service);

        $methods = $service->getMethods();
        foreach ($methods as $method) {
            $this->validateMethodReturnType($method);

            $serviceClassMethodImplementation = $this->builderFactory
                ->method($method->getName())
                ->makePublic();

            $this->appendAttributes($method->getAttributes(), $serviceClassMethodImplementation);

            $methodParameters = $this->appendMethodParameters($method);
            $serviceClassMethodImplementation->addParams($methodParameters);

            $serviceClassMethodImplementation->setReturnType(Utils::toFQCN($method->getReturnType()->getName()));
            $serviceClassMethodImplementation->addStmt(new Return_($serviceMethodInvokeReturnStmt));

            $serviceClassImplementation->addStmt($serviceClassMethodImplementation->getNode());
        }
    }

    private function wrapInNamespace(string $namespace, Class_ $serviceClassImplementation): Namespace_
    {
        return $this->builderFactory
            ->namespace($namespace)
            ->addStmt($serviceClassImplementation);
    }

    private function createServiceMethodInvokeReturnStmt(ReflectionClass $service): MethodCall
    {
        $serviceMethodFactory = new StaticCall(
            new Name(Utils::toFQCN(ServiceMethodFactory::class)),
            'create',
            [
                new PropertyFetch(new Variable('this'), 'retrofit'),
                new String_(Utils::toFQCN($service->getName())),
                new Function_(),
            ]
        );
        return new MethodCall(
            $serviceMethodFactory,
            'invoke',
            [
                new FuncCall(new Name('func_get_args')),
            ]
        );
    }

    private function validateMethodReturnType(ReflectionMethod $method): void
    {
        if (!$method->hasReturnType()) {
            throw Utils::methodException($method, 'Method return type is required, none found.');
        }

        $returnType = $method->getReturnType()->getName();
        $callClassReturnType = Call::class;
        if ($returnType !== $callClassReturnType) {
            throw Utils::methodException($method,
                "Method return type should be a {$callClassReturnType} class. '{$returnType}' return type found.");
        }
    }

    private function appendMethodParameters(ReflectionMethod $method): array
    {
        $params = [];
        foreach ($method->getParameters() as $parameter) {
            $paramBuilder = $this->builderFactory->param($parameter->name);

            if ($parameter->isDefaultValueAvailable()) {
                $paramBuilder->setDefault($parameter->getDefaultValue());
            }

            if ($parameter->getType() === null) {
                throw Utils::parameterException($method, $parameter->getPosition(),
                    "Parameter type is required, none found.");
            }

            $reflectionTypeName = $parameter->getType()->getName();
            if (!($parameter->getType()->isBuiltin())) {
                $reflectionTypeName = Utils::toFQCN($reflectionTypeName);
            }

            $type = $parameter->getType()->allowsNull() ? new NullableType($reflectionTypeName) : $reflectionTypeName;
            $paramBuilder->setType($type);

            if ($parameter->isPassedByReference()) {
                $paramBuilder->makeByRef();
            }

            if ($parameter->isVariadic()) {
                $paramBuilder->makeVariadic();
            }

            $this->appendAttributes($parameter->getAttributes(), $paramBuilder);

            $params[] = $paramBuilder->getNode();
        }
        return $params;
    }

    /**
     * @param ReflectionAttribute[] $attributes
     */
    private function appendAttributes(array $attributes, Method|Param $destination): void
    {
        foreach ($attributes as $attribute) {
            $name = new Name(Utils::toFQCN($attribute->getName()));
            $attribute = $this->builderFactory->attribute($name, $attribute->getArguments());

            $destination->addAttribute($attribute);
        }
    }
}
