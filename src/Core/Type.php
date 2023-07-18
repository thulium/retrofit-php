<?php

declare(strict_types=1);

namespace Retrofit\Core;

use Ouzo\Utilities\Arrays;
use phpDocumentor\Reflection\DocBlock\Tags\Param;
use phpDocumentor\Reflection\Types\Object_;
use PhpParser\Node;
use PhpParser\Node\Stmt\Use_;
use PhpParser\NodeFinder;
use PhpParser\ParserFactory;
use ReflectionMethod;
use ReflectionParameter;

readonly class Type
{
    private const SCALARS = ['bool', 'int', 'float', 'string'];

    public function __construct(
        private string $rawType,
        private ?string $parametrizedType = null,
    ) {
    }

    public function getRawType(): string
    {
        return $this->rawType;
    }

    public function getParametrizedType(): ?string
    {
        return $this->parametrizedType;
    }

    public function isScalar(): bool
    {
        return in_array($this->rawType, self::SCALARS);
    }

    public function isA(string $type): bool
    {
        return $this->rawType === $type;
    }

    public function parametrizedTypeIsScalar(): bool
    {
        return !is_null($this->parametrizedType) && in_array($this->parametrizedType, self::SCALARS);
    }

    /** @param Param[] $params */
    public static function create(
        ReflectionMethod $reflectionMethod,
        ReflectionParameter $reflectionParameter,
        array $params = [],
    ): Type {
        $reflectionType = $reflectionParameter->getType();

        $rawType = $reflectionType->getName();
        $parametrizedType = self::handleParametrizedTypeForArray($rawType, $reflectionParameter, $reflectionMethod, $params);

        return new Type($rawType, $parametrizedType);
    }

    public function __toString(): string
    {
        return is_null($this->parametrizedType) ? $this->rawType : "{$this->rawType}<{$this->parametrizedType}>";
    }

    private static function handleParametrizedTypeForArray(
        string $rawType,
        ReflectionParameter $reflectionParameter,
        ReflectionMethod $reflectionMethod,
        array $params,
    ): ?string {
        if ($rawType !== 'array') {
            return null;
        }

        $parameterName = $reflectionParameter->getName();
        /** @var Param|null $param */
        $param = Arrays::find($params, fn(Param $p): ?Param => $p->getVariableName() === $parameterName ? $p : null);
        if (is_null($param)) {
            return null;
        }

        $paramType = $param->getType()->getValueType();
        if ($paramType instanceof Object_) {
            return self::handleParametrizedTypeOfObject($paramType, $reflectionMethod);
        }

        return $paramType->__toString();
    }

    private static function handleParametrizedTypeOfObject(Object_ $paramType, ReflectionMethod $reflectionMethod): string
    {
        $fqsen = $paramType->getFqsen();
        if (is_null($fqsen)) {
            return $paramType->__toString();
        }

        $parametrizedType = $fqsen->getName();
        $content = file_get_contents($reflectionMethod->getDeclaringClass()->getFileName());

        $parserFactory = new ParserFactory();
        $parser = $parserFactory->create(ParserFactory::PREFER_PHP7);
        $stmts = $parser->parse($content);

        $nodeFinder = new NodeFinder();
        $useStmt = $nodeFinder->findFirst($stmts, fn(Node $n): bool => $n instanceof Use_ && $parametrizedType === $n->uses[0]->name->getLast());

        return $useStmt->uses[0]->name->toString();
    }
}
