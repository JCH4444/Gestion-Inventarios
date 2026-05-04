<?php
namespace App\Validators;

class ProviderValidator
{
    /**
     * Valida los datos de un proveedor
     */
    public static function validateProviderInput(
        string $nombre_proveedor,
        string $representante_proveedor,
        string $correo_proveedor,
        ?string $telefono_proveedor = null,
        string $estado_proveedor = 'ACTIVO'
    ): array {
        $errors = [];
        
        if (trim($nombre_proveedor) === '') {
            $errors[] = 'El nombre de la empresa es obligatorio.';
        }
        
        if (trim($representante_proveedor) === '') {
            $errors[] = 'El representante es obligatorio.';
        }
        
        if (trim($correo_proveedor) === '') {
            $errors[] = 'El correo es obligatorio.';
        } elseif (!filter_var($correo_proveedor, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'El correo no es válido.';
        }
        
        if ($estado_proveedor !== 'ACTIVO' && $estado_proveedor !== 'INACTIVO') {
            $errors[] = 'El estado debe ser ACTIVO o INACTIVO.';
        }
        
        return $errors;
    }
}