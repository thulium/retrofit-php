<?php
declare(strict_types=1);

namespace Retrofit\Tests\Fixtures\Api;

use Retrofit\Attribute\DELETE;
use Retrofit\Attribute\GET;
use Retrofit\Attribute\HEAD;
use Retrofit\Attribute\OPTIONS;
use Retrofit\Attribute\PATCH;
use Retrofit\Attribute\POST;
use Retrofit\Attribute\PUT;
use Retrofit\Attribute\Response\ResponseBody;
use Retrofit\Call;

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
