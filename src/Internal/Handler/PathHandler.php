<?php
declare(strict_types=1);

namespace Retrofit\Internal\Handler;

use Retrofit\Attribute\Path;
use Retrofit\Internal\RequestBuilder;
use RuntimeException;

class PathHandler implements ParameterHandler
{
    private Path $path;

    public function __construct()
    {
    }

    public function setAttribute(Path $instance): void
    {
        $this->path = $instance;
    }

    public function apply(RequestBuilder $requestBuilder, mixed $value): void
    {
        if ($value === null) {
            throw new RuntimeException('Path parameters cannot be null');
        }

//        $this->

//        $requestBuilder->replacePath($this->name, $this->converter->convert($value));
    }
}
