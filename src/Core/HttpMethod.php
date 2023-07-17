<?php
declare(strict_types=1);

namespace Retrofit\Core;

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
