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
use Retrofit\Call;

interface AllHttpRequestMethods
{
    #[DELETE('/delete')]
    public function delete(): Call;

    #[GET('/get')]
    public function get(): Call;

    #[HEAD('/head')]
    public function head(): Call;

    #[OPTIONS('/options')]
    public function options(): Call;

    #[PATCH('/patch')]
    public function patch(): Call;

    #[POST('/post')]
    public function post(): Call;

    #[PUT('/put')]
    public function put(): Call;
}
