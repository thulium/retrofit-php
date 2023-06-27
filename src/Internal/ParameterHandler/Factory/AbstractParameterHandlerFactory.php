<?php
declare(strict_types=1);

namespace Retrofit\Internal\ParameterHandler\Factory;

use Retrofit\Attribute\Path;
use Retrofit\Internal\ConverterProvider;
use Retrofit\Internal\ParameterHandler\ParameterHandler;

readonly abstract class AbstractParameterHandlerFactory
{
    public function __construct(protected ConverterProvider $converterProvider)
    {
    }

    abstract public function create(string $name, Path $path): ParameterHandler;
}
