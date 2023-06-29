<?php
declare(strict_types=1);

namespace Retrofit\Internal\Utils;

use Ouzo\Utilities\Strings;
use ReflectionMethod;
use RuntimeException;
use TRegx\CleanRegex\Match\Detail;

/**
 * Convenient utils for repeatable things.
 *
 * <b>Only for internal purposes.</b>
 */
readonly class Utils
{
    private const NAMESPACE_DELIMITER = '\\';
    private const PARAM_URL_REGEX = '\{([a-zA-Z][a-zA-Z0-9_-]*)\}';

    private function __construct()
    {
    }

    /**
     * Transforms {@code $names} to the valid FQCN (Full Qualified Class Name) with leading namespace delimiter.
     */
    public static function toFQCN(string...$names): string
    {
        return collect($names)
            ->map(fn(string $name): string => str_starts_with($name, self::NAMESPACE_DELIMITER) ? $name : (self::NAMESPACE_DELIMITER . $name))
            ->join(Strings::EMPTY);
    }

    /**
     * Creates an exception with the message which contains a detailed info about method where an error occurs.
     */
    public static function methodException(ReflectionMethod $reflectionMethod, string $message): RuntimeException
    {
        $methodExceptionMessage = self::methodExceptionMessage($reflectionMethod);
        return new RuntimeException("{$methodExceptionMessage}. {$message}");
    }

    /**
     * Creates exception with message which contains a detailed info about method and parameter number where error occurs.
     */
    public static function parameterException(ReflectionMethod $reflectionMethod, int $position, string $message): RuntimeException
    {
        $methodExceptionMessage = self::methodExceptionMessage($reflectionMethod);
        $position += 1;
        return new RuntimeException("{$methodExceptionMessage} parameter #{$position}. {$message}");
    }

    /**
     * Gets the set of unique path parameters used in the given URI. If a parameter is used twice in the URI, it will
     * only show up once in the set.
     *
     * @return string[]
     */
    public static function parsePathParameters(string $path): array
    {
        /** @var Detail[] $matcher */
        $matcher = pattern(self::PARAM_URL_REGEX)
            ->match($path);
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
