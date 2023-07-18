<?php

declare(strict_types=1);

namespace Retrofit\Tests\Fixtures\Api;

use Retrofit\Core\Attribute\DELETE;
use Retrofit\Core\Attribute\GET;
use Retrofit\Core\Attribute\HEAD;
use Retrofit\Core\Attribute\OPTIONS;
use Retrofit\Core\Attribute\PATCH;
use Retrofit\Core\Attribute\POST;
use Retrofit\Core\Attribute\PUT;
use Retrofit\Core\Attribute\Response\ResponseBody;
use Retrofit\Core\Call;

interface AllHttpRequestMethods
{
    #[DELETE('/delete')]
    #[ResponseBody('string')]
    public function delete(): Call;

    #[GET('/get')]
    #[ResponseBody('string')]
    public function get(): Call;

    #[HEAD('/head')]
    #[ResponseBody('string')]
    public function head(): Call;

    #[OPTIONS('/options')]
    #[ResponseBody('string')]
    public function options(): Call;

    #[PATCH('/patch')]
    #[ResponseBody('string')]
    public function patch(): Call;

    #[POST('/post')]
    #[ResponseBody('string')]
    public function post(): Call;

    #[PUT('/put')]
    #[ResponseBody('string')]
    public function put(): Call;
}
