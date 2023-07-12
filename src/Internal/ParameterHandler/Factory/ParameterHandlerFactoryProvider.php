<?php
declare(strict_types=1);

namespace Retrofit\Internal\ParameterHandler\Factory;

use Retrofit\Attribute\Body;
use Retrofit\Attribute\Field;
use Retrofit\Attribute\FieldMap;
use Retrofit\Attribute\Header;
use Retrofit\Attribute\HeaderMap;
use Retrofit\Attribute\Part;
use Retrofit\Attribute\PartMap;
use Retrofit\Attribute\Path;
use Retrofit\Attribute\Query;
use Retrofit\Attribute\QueryMap;
use Retrofit\Attribute\QueryName;
use Retrofit\Attribute\Url;
use Retrofit\Internal\ConverterProvider;

class ParameterHandlerFactoryProvider
{
    private array $attributeNameToFactory;

    public function __construct(ConverterProvider $converterProvider)
    {
        $this->attributeNameToFactory = [
            Body::class => new BodyParameterHandlerFactory($converterProvider),
            Field::class => new FieldParameterHandlerFactory($converterProvider),
            FieldMap::class => new FieldMapParameterHandlerFactory($converterProvider),
            Header::class => new HeaderParameterHandlerFactory($converterProvider),
            HeaderMap::class => new HeaderMapParameterHandlerFactory($converterProvider),
            Part::class => new PartParameterHandlerFactory($converterProvider),
            PartMap::class => new PartMapParameterHandlerFactory($converterProvider),
            Path::class => new PathParameterHandlerFactory($converterProvider),
            Query::class => new QueryParameterHandlerFactory($converterProvider),
            QueryMap::class => new QueryMapParameterHandlerFactory($converterProvider),
            QueryName::class => new QueryNameParameterHandlerFactory($converterProvider),
            Url::class => new UrlParameterHandlerFactory($converterProvider),
        ];
    }

    public function get(string $attributeName): AbstractParameterHandlerFactory
    {
        return $this->attributeNameToFactory[$attributeName];
    }
}
