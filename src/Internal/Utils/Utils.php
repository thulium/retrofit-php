<?php
declare(strict_types=1);

namespace Retrofit\Internal\Utils;

use ReflectionMethod;
use RuntimeException;
use TRegx\CleanRegex\Match\Detail;

readonly class Utils
{
    public const NAMESPACE_DELIMITER = '\\';

    private const PARAM_URL_REGEX = '\{([a-zA-Z][a-zA-Z0-9_-]*)\}';

    private function __construct()
    {
    }

    public static function toFQCN(string $name): string
    {
        $startsWithNamespaceDelimiter = str_starts_with($name, self::NAMESPACE_DELIMITER);
        if ($startsWithNamespaceDelimiter) {
            return $name;
        }
        return self::NAMESPACE_DELIMITER . $name;
    }

    public static function methodException(ReflectionMethod $reflectionMethod, string $message): RuntimeException
    {
        $methodExceptionMessage = self::methodExceptionMessage($reflectionMethod);
        $msg = "{$methodExceptionMessage}. {$message}";
        return new RuntimeException($msg);
    }

    public static function parameterException(ReflectionMethod $reflectionMethod, int $position, string $message): RuntimeException
    {
        $methodExceptionMessage = self::methodExceptionMessage($reflectionMethod);
        $position += 1;
        $msg = "{$methodExceptionMessage} parameter #{$position}. {$message}";
        return new RuntimeException($msg);
    }

    /**
     * Gets the set of unique path parameters used in the given URI. If a parameter is used twice in the URI, it will
     * only show up once in the set.
     */
    public static function parsePathParameters(string $path): array
    {
        /** @var Detail[] $matcher */
        $matcher = pattern(self::PARAM_URL_REGEX)->match($path);
        $patterns = [];
        foreach ($matcher as $detail) {
            $patterns[] = $detail->get(1);
        }
        return array_unique($patterns);
    }

    private static function methodExceptionMessage(ReflectionMethod $reflectionMethod): string
    {
        $className = $reflectionMethod->getDeclaringClass()->getShortName();
        $methodName = $reflectionMethod->getShortName();
        return "Method {$className}::{$methodName}()";
    }
}
