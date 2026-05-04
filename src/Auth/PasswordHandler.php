<?php
namespace App\Auth;

class PasswordHandler
{
    /**
     * Encripta una contraseña usando BCRYPT
     */
    public static function hash(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    /**
     * Verifica una contraseña contra su hash
     */
    public static function verify(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}