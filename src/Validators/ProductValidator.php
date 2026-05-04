<?php
namespace App\Validators;

class ProductValidator
{
    /**
     * Valida los datos de un producto
     */
    public static function validateProductInput(
        string $codigo_barras,
        string $nombre_producto,
        string $costo_unitario,
        string $cantidad_inicial,
        string $stock_minimo,
        ?string $descripcion_producto = null,
        string $estado_producto = 'ACTIVO'
    ): array {
        $errors = [];
        
        if (trim($codigo_barras) === '') {
            $errors[] = 'El código de barras es obligatorio.';
        }
        
        if (trim($nombre_producto) === '') {
            $errors[] = 'El nombre del producto es obligatorio.';
        }
        
        if ($costo_unitario === '') {
            $errors[] = 'El costo unitario es obligatorio.';
        } elseif (!is_numeric($costo_unitario) || (float)$costo_unitario <= 0) {
            $errors[] = 'El costo debe ser un número positivo.';
        }
        
        if ($cantidad_inicial === '') {
            $errors[] = 'La cantidad inicial es obligatoria.';
        } elseif (!is_numeric($cantidad_inicial) || (int)$cantidad_inicial < 0) {
            $errors[] = 'La cantidad debe ser un número no negativo.';
        }
        
        if ($stock_minimo === '') {
            $errors[] = 'El stock mínimo es obligatorio.';
        } elseif (!is_numeric($stock_minimo) || (int)$stock_minimo < 0) {
            $errors[] = 'El stock mínimo debe ser un número no negativo.';
        }
        
        if ($estado_producto !== 'ACTIVO' && $estado_producto !== 'INACTIVO') {
            $errors[] = 'El estado debe ser ACTIVO o INACTIVO.';
        }
        
        return $errors;
    }
}