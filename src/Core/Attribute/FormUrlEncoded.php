<?php

declare(strict_types=1);

namespace Retrofit\Core\Attribute;

use Attribute;

/**
 * Denotes that the request body will use form URL encoding. Fields should be declared as parameters and annotated with
 * {@link Field} or {@link FieldMap}.
 * Requests made with this annotation will have <code>application/x-www-form-urlencoded</code> MIME type.
 *
 * @see Field
 * @see FieldMap
 *
 * @api
 */
#[Attribute(Attribute::TARGET_METHOD)]
readonly class FormUrlEncoded
{
}
