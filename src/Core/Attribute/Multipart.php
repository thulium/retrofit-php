<?php

declare(strict_types=1);

namespace Retrofit\Core\Attribute;

use Attribute;

/**
 * Denotes that the request body is multi-part. Parts should be declared as parameters and annotated with {@link Part}.
 *
 * @see Part
 * @see PartMap
 *
 * @api
 */
#[Attribute(Attribute::TARGET_METHOD)]
readonly class Multipart
{
}
