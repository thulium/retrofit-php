<?php
declare(strict_types=1);

namespace Retrofit\Internal\ParameterHandler;

use InvalidArgumentException;
use Retrofit\Attribute\Path;
use Retrofit\Internal\RequestBuilder;

class PathHandler implements ParameterHandler
{
    private Path $path;
    private string $name;

    public function __construct()
    {
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setAttribute(Path $instance): void
    {
        $this->path = $instance;
    }

    public function apply(RequestBuilder $requestBuilder, mixed $value): void
    {
        if (is_null($value)) {
            throw new InvalidArgumentException("Path parameter '{$this->name}' value must not be null");
        }

        //todo use converter
        $requestBuilder->addPathParam($this->name, (string)$value, $this->path->encoded());
    }
}
