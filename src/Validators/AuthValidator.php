<?php
namespace App\Validators;

class AuthValidator
{
    /**
     * Valida que email y contraseña no estén vacíos
     */
    public static function validateLoginInput(string $email, string $password): array
    {
        $errors = [];
        
        if (trim($email) === '') {
            $errors[] = 'El email es obligatorio.';
        }
        
        if ($password === '') {
            $errors[] = 'La contraseña es obligatoria.';
        }
        
        return $errors;
    }

    /**
     * Valida que todos los campos de registro sean válidos
     */
    public static function validateRegisterInput(
        string $documento,
        string $nombres,
        string $apellidos,
        string $correo,
        string $contra,
        ?string $telefono = null
    ): array {
        $errors = [];
        
        if (trim($documento) === '') {
            $errors[] = 'El documento es obligatorio.';
        }
        
        if (trim($nombres) === '') {
            $errors[] = 'Los nombres son obligatorios.';
        }
        
        if (trim($apellidos) === '') {
            $errors[] = 'Los apellidos son obligatorios.';
        }
        
        if (trim($correo) === '') {
            $errors[] = 'El correo es obligatorio.';
        } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'El correo no es válido.';
        }
        
        if ($contra === '') {
            $errors[] = 'La contraseña es obligatoria.';
        } elseif (strlen($contra) < 6) {
            $errors[] = 'La contraseña debe tener al menos 6 caracteres.';
        }
        
        return $errors;
    }
}