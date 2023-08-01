<?php

declare(strict_types=1);

namespace Retrofit\Core\Attribute;

use Attribute;

/**
 * Treat the response body on methods returning {@link StreamInterface} as is, i.e. without converting the body.
 *
 * @api
 */
#[Attribute(Attribute::TARGET_METHOD)]
readonly class Streaming
{
}
