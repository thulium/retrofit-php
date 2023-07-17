<?php
declare(strict_types=1);

namespace Retrofit\Core\Internal\ParameterHandler\Factory;

use ReflectionMethod;
use Retrofit\Core\Attribute\Body;
use Retrofit\Core\Attribute\Field;
use Retrofit\Core\Attribute\FieldMap;
use Retrofit\Core\Attribute\Header;
use Retrofit\Core\Attribute\HeaderMap;
use Retrofit\Core\Attribute\HttpRequest;
use Retrofit\Core\Attribute\Part;
use Retrofit\Core\Attribute\PartMap;
use Retrofit\Core\Attribute\Path;
use Retrofit\Core\Attribute\Query;
use Retrofit\Core\Attribute\QueryMap;
use Retrofit\Core\Attribute\QueryName;
use Retrofit\Core\Attribute\Url;
use Retrofit\Core\Internal\ConverterProvider;
use Retrofit\Core\Internal\Encoding;
use Retrofit\Core\Internal\ParameterHandler\ParameterHandler;
use Retrofit\Core\Type;

readonly abstract class AbstractParameterHandlerFactory
{
    public function __construct(protected ConverterProvider $converterProvider)
    {
    }

    abstract public function create(
        Body & Field & FieldMap & Header & HeaderMap & Part & PartMap & Path & Query & QueryMap & QueryName & Url $param,
        HttpRequest $httpRequest,
        ?Encoding $encoding,
        ReflectionMethod $reflectionMethod,
        int $position,
        Type $type
    ): ParameterHandler;
}
