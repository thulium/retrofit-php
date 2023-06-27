<?php
declare(strict_types=1);

namespace Retrofit\Internal\Utils;

readonly class ReflectionUtils
{
    public const NAMESPACE_DELIMITER = '\\';

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
}
