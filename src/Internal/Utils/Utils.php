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
        $msg = "Method {$reflectionMethod->getDeclaringClass()->getShortName()}::{$reflectionMethod->getShortName()}(). {$message}";
        return new RuntimeException($msg);
    }

    public static function parameterException(string $message): RuntimeException
    {
        return new RuntimeException($message);
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
}
