<?php

declare(strict_types=1);

namespace Retrofit\Core\Converter;

use Retrofit\Core\Type;

/**
 * Creates {@link RequestBodyConverter}, {@link ResponseBodyConverter} or {@link StringConverter} instances based on a
 * type and target usage.
 *
 * @see RequestBodyConverter
 * @see ResponseBodyConverter
 * @see StringConverter
 *
 * @api
 */
interface ConverterFactory
{
    /**
     * Returns a {@link RequestBodyConverter} for converting an HTTP response body to {@link Type} or <code>null</code>
     * if {@link Type} cannot be handled by this factory.
     */
    public function requestBodyConverter(Type $type): ?RequestBodyConverter;

    /**
     * Returns a {@link ResponseBodyConverter} for converting {@link Type} to an HTTP request body or <code>null</code>
     * if {@link Type} cannot be handled by this factory. This is used to create converters for types
     * specified by {@link Body}, {@link Part} and {@link PartMap} values.
     */
    public function responseBodyConverter(Type $type): ?ResponseBodyConverter;

    /**
     * Returns a {@link StringConverter} for converting {@link Type} to a <code>string</code> or <code>null</code> if
     * {@link Type}  cannot be handled by this factory. This is used to create converters for types
     * specified by {@link Field}, {@link FieldMap} values, {@link Header}, {@link HeaderMap}, {@link Path},
     * {@link Query}, and {@link QueryMap} values.
     */
    public function stringConverter(Type $type): ?StringConverter;
}
