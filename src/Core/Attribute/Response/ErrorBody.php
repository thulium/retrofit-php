<?php

declare(strict_types=1);

namespace Retrofit\Core\Attribute\Response;

use Attribute;

/**
 * Use this attribute to define a class that errors should be deserialized as.
 *
 * @api
 */
#[Attribute(Attribute::TARGET_METHOD)]
readonly class ErrorBody
{
    use WithBody;
}
