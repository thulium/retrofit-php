<?php

declare(strict_types=1);

namespace Retrofit\Tests\Fixtures\Model;

use JsonSerializable;

class UserRequest implements JsonSerializable
{
    private int $id;

    private string $login;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;
        return $this;
    }

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
            'id' => $this->id,
            'login' => $this->login,
        ];
    }
}
