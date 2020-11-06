<?php


namespace core\crypto;


class PasswordHash
{
    private string $hash;

    public function __construct($hash)
    {
        $this->hash = $hash;
    }

    public static function fromPassword(string $password): PasswordHash
    {
        return new PasswordHash(password_hash($password, PASSWORD_DEFAULT));
    }
    
    public function check(string $password): bool 
    {
        return password_verify($password, $this->hash);
    }

    public function getHash(): string
    {
        return $this->hash;
    }
}