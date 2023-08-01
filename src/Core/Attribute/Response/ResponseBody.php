<?php

declare(strict_types=1);

namespace Retrofit\Core\Attribute\Response;

use Attribute;

/**
 * Use this annotation to define a class that responses should be deserialized as.
 *
 * @api
 */
#[Attribute(Attribute::TARGET_METHOD)]
readonly class ResponseBody
{
    use WithBody;
}
