<?php
declare(strict_types=1);

namespace Retrofit\Internal\ParameterHandler\Factory;

use Retrofit\Attribute\Path;
use Retrofit\Internal\ParameterHandler\ParameterHandler;
use Retrofit\Internal\ParameterHandler\PathParameterHandler;

readonly class PathAbstractParameterHandlerFactory extends AbstractParameterHandlerFactory
{
    public function create(string $name, Path $path): ParameterHandler
    {
        $converter = $this->converterProvider->getStringConverter();
        return new PathParameterHandler($name, $path->encoded(), $converter);
    }
}
