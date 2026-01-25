<?php
namespace Framework\Core;

class Hash {
    /**
     * Encripta una contraseña usando BCRYPT
     */
    public static function make(string $password): string {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
    }

    /**
     * Verifica si la contraseña coincide con el hash almacenado
     */
    public static function verify(string $password, string $hash): bool {
        return password_verify($password, $hash);
    }
}