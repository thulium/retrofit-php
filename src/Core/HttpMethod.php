<?php

declare(strict_types=1);

namespace Retrofit\Core;

/**
 * Convenient enum to describe HTTP methods.
 *
 * @api
 */
enum HttpMethod: string
{
    case GET = 'GET';
    case HEAD = 'HEAD';
    case POST = 'POST';
    case PUT = 'PUT';
    case PATCH = 'PATCH';
    case DELETE = 'DELETE';
    case OPTIONS = 'OPTIONS';
    case TRACE = 'TRACE';
}
