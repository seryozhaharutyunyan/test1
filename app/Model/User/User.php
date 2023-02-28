<?php

namespace App\Model\User;

use Engine\Core\Database\UserActiveRecord;

class User
{
    use UserActiveRecord;

    protected string $table = 'users';
    protected int $id;
    private string $email;
    private string $password;
    private ?string $token;
    private string $role;
    private string $create_at;


    public function getId(): int
    {
        return $this->id;
    }


    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @param string|null $token
     */
    public function setToken(string|null $token): void
    {
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getRole(): string
    {
        return $this->role;
    }

    /**
     * @param string $role
     */
    public function setRole(string $role): void
    {
        $this->role = $role;
    }

    /**
     * @return string
     */
    public function getCreateAt(): string
    {
        return $this->create_at;
    }

    /**
     * @param string $create_at
     */
    public function setCreateAt(string $create_at): void
    {
        $this->create_at = $create_at;
    }
}