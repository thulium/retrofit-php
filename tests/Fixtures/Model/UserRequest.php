<?php

declare(strict_types=1);

namespace Retrofit\Tests\Fixtures\Model;

use JsonSerializable;

class UserRequest implements JsonSerializable
{
    private string $login;

    public function getLogin(): string
    {
        return $this->login;
    }

    public function setLogin(string $login): static
    {
        $this->login = $login;
        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'login' => $this->login,
        ];
    }
}
