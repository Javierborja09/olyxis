<?php
namespace Framework\Core;

/**
 * Clase Hash
 * * Proporciona métodos estáticos para el manejo seguro de contraseñas.
 * Utiliza el algoritmo BCRYPT, que es el estándar recomendado para el 
 * hashing de credenciales debido a su costo computacional ajustable.
 */
class Hash {
    /**
     * Encripta una contraseña usando el algoritmo BCRYPT.
     * * El "cost" de 10 es un equilibrio entre seguridad y rendimiento.
     * Genera un hash único de 60 caracteres que incluye la sal (salt) automáticamente.
     * * @param string $password La contraseña en texto plano.
     * @return string El hash generado listo para guardar en la BD.
     */
    public static function make(string $password): string {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
    }

    /**
     * Verifica si una contraseña en texto plano coincide con un hash guardado.
     * * Este método es seguro contra ataques de temporización (timing attacks).
     * * @param string $password La contraseña enviada por el usuario (ej: desde un form).
     * @param string $hash El hash recuperado de la base de datos.
     * @return bool True si la contraseña es correcta, false en caso contrario.
     */
    public static function verify(string $password, string $hash): bool {
        return password_verify($password, $hash);
    }
}