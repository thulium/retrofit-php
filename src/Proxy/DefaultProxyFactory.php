<?php
declare(strict_types=1);

namespace Retrofit\Proxy;

use LogicException;
use PhpParser\Builder\Method;
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
use ReflectionClass;
use ReflectionMethod;
use Retrofit\Call;
use Retrofit\Internal\ServiceMethodFactory;
use Retrofit\Internal\Utils\ReflectionUtils;
use Retrofit\Retrofit;

class DefaultProxyFactory implements ProxyFactory
{
    private const PROXY_PREFIX = 'Retrofit\Proxy\\';

    public function __construct(
        private readonly BuilderFactory $builderFactory,
        private readonly PrettyPrinterAbstract $prettyPrinterAbstract
    )
    {
    }

    /**
     * Create a new proxy class given an interface name.
     */
    public function create(Retrofit $retrofit, ReflectionClass $service): ?object
    {
        $proxyServiceName = "{$service->getShortName()}Impl";
        $serviceFQCN = ReflectionUtils::toFQCN($service->getName());
        $proxyServiceBuilder = $this->builderFactory
            ->class($proxyServiceName)
            ->implement($serviceFQCN);

        $param1 = $this->builderFactory
            ->param('retrofit')
            ->makePrivate()
            ->setType(ReflectionUtils::toFQCN(Retrofit::class));
//        $param2 = $this->builderFactory
//            ->param('baseUrl')
//            ->makePrivate()
//            ->setType(ReflectionUtils::toFQCN(UriInterface::class));
        $constructor = $this->builderFactory
            ->method('__construct')
            ->makePublic()
            ->addParam($param1->getNode());
//            ->addParam($param2->getNode());
        $proxyServiceBuilder->addStmt($constructor->getNode());

        $proxyServiceMethod = $this->createProxyServiceMethod($service);

        $methods = $service->getMethods();
        foreach ($methods as $method) {
            $this->validateReturnType($method, $service);

            $proxyMethodBuilder = $this->builderFactory
                ->method($method->getName())
                ->makePublic();

            $this->attributes($method->getAttributes(), $proxyMethodBuilder);

            $params = $this->params($method, $service);
            $proxyMethodBuilder->addParams($params);

            $proxyMethodBuilder->setReturnType(ReflectionUtils::toFQCN($method->getReturnType()->getName()));
            $proxyMethodBuilder->addStmt(new Return_($proxyServiceMethod));

            $proxyServiceBuilder->addStmt($proxyMethodBuilder->getNode());
        }

        $namespace = self::PROXY_PREFIX . $service->getNamespaceName();
        $proxyNamespaceBuilder = $this->builderFactory
            ->namespace($namespace)
            ->addStmt($proxyServiceBuilder);

        $proxyServiceClass = $this->prettyPrinterAbstract->prettyPrint([$proxyNamespaceBuilder->getNode()]);

        eval($proxyServiceClass);

        $proxyServiceFQCN = ReflectionUtils::toFQCN($namespace . ReflectionUtils::NAMESPACE_DELIMITER . $proxyServiceName);
        return new $proxyServiceFQCN($retrofit);
    }

    private function attributes(array $attributes, Method|Param $destination): void
    {
        foreach ($attributes as $attribute) {
            $name = new Name(ReflectionUtils::toFQCN($attribute->getName()));
            $attribute = $this->builderFactory->attribute($name, $attribute->getArguments());

            $destination->addAttribute($attribute);
        }
    }

    private function params(ReflectionMethod $method, ReflectionClass $service): array
    {
        $params = [];
        foreach ($method->getParameters() as $parameter) {
            $paramBuilder = $this->builderFactory->param($parameter->name);

            if ($parameter->isDefaultValueAvailable()) {
                $paramBuilder->setDefault($parameter->getDefaultValue());
            }

            if ($parameter->getType() === null) {
                throw new LogicException(
                    "Parameter types are required. " .
                    "None found for parameter {$parameter->getName()} in {$service->getShortName()}::{$method->getShortName()}()."
                );
            }

            $reflectionTypeName = $parameter->getType()->getName();
            if (!($parameter->getType()->isBuiltin())) {
                $reflectionTypeName = ReflectionUtils::NAMESPACE_DELIMITER . $reflectionTypeName;
            }

            $type = $parameter->getType()->allowsNull() ? new NullableType($reflectionTypeName) : $reflectionTypeName;
            $paramBuilder->setType($type);

            if ($parameter->isPassedByReference()) {
                $paramBuilder->makeByRef();
            }

            if ($parameter->isVariadic()) {
                $paramBuilder->makeVariadic();
            }

            $this->attributes($parameter->getAttributes(), $paramBuilder);

            $params[] = $paramBuilder->getNode();
        }
        return $params;
    }

    private function createProxyServiceMethod(ReflectionClass $service): MethodCall
    {
        $serviceMethodFactory = new StaticCall(
            new Name(ReflectionUtils::toFQCN(ServiceMethodFactory::class)),
            'create',
            [
                new PropertyFetch(new Variable('this'), 'retrofit'),
//                new PropertyFetch(new Variable('this'), 'baseUrl'),
                new String_(ReflectionUtils::toFQCN($service->getName())),
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

    private function validateReturnType(ReflectionMethod $method, ReflectionClass $service): void
    {
        if (!$method->hasReturnType()) {
            throw new LogicException(
                "Method return types are required. " .
                "None found for {$service->getShortName()}::{$method->getShortName()}()."
            );
        }

        $returnType = $method->getReturnType()->getName();
        $callClassReturnType = Call::class;
        if ($returnType !== $callClassReturnType) {
            throw new LogicException(
                "Method return type should be a {$callClassReturnType} class. " .
                "'{$returnType}' return type found for {$service->getShortName()}::{$method->getShortName()}()."
            );
        }
    }
}
