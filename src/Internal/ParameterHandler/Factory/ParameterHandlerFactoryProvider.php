<?php
declare(strict_types=1);

namespace Retrofit\Internal\ParameterHandler\Factory;

use Retrofit\Attribute\Path;
use Retrofit\Attribute\Url;
use Retrofit\Internal\ConverterProvider;

class ParameterHandlerFactoryProvider
{
    private static array $attributeNameToFactory;

    public function __construct(ConverterProvider $converterProvider)
    {
        self::$attributeNameToFactory = [
            Path::class => new PathParameterHandlerFactory($converterProvider),
            Url::class => new UrlParameterHandlerFactory($converterProvider),
        ];
    }

    public function get(string $attributeName): AbstractParameterHandlerFactory
    {
        return self::$attributeNameToFactory[$attributeName];
    }
}
