<?php
namespace Tests\Unit\Validators;

use App\Validators\ProductValidator;
use PHPUnit\Framework\TestCase;

class ProductValidatorTest extends TestCase
{
    /**
     * TEST 1: Verifica que producto con datos VÁLIDOS NO genera errores
     * 
     * Entrada: Código de barras, nombre, costo, cantidad, stock mínimo - TODOS válidos
     * Resultado esperado: Array vacío (sin errores)
     */
    public function testValidProductInput(): void
    {
        $errors = ProductValidator::validateProductInput(
            'EAN123456789',      // código de barras válido
            'Producto Test',     // nombre válido
            '25.50',             // costo válido (positivo)
            '100',               // cantidad válida (no negativa)
            '10'                 // stock mínimo válido (no negativo)
        );
        $this->assertEmpty($errors); // ✅ Debe estar vacío
    }

    /**
     * TEST 2: Verifica que código de barras VACÍO genera error
     * 
     * Entrada: Código de barras vacío + otros campos válidos
     * Resultado esperado: Array con al menos 1 error
     */
    public function testProductInputEmptyBarcode(): void
    {
        $errors = ProductValidator::validateProductInput(
            '',                  // código de barras VACÍO
            'Producto Test',
            '25.50',
            '100',
            '10'
        );
        $this->assertNotEmpty($errors); // ✅ Debe haber error
    }

    /**
     * TEST 3: Verifica que nombre VACÍO genera error
     * 
     * Entrada: Nombre producto vacío + otros campos válidos
     * Resultado esperado: Array con al menos 1 error
     */
    public function testProductInputEmptyName(): void
    {
        $errors = ProductValidator::validateProductInput(
            'EAN123456789',
            '',                  // nombre VACÍO
            '25.50',
            '100',
            '10'
        );
        $this->assertNotEmpty($errors); // ✅ Debe haber error
    }

    /**
     * TEST 4: Verifica que costo INVÁLIDO (texto) genera error
     * 
     * Entrada: Costo con valor que NO es número + otros campos válidos
     * Resultado esperado: Array con error que contiene "número"
     */
    public function testProductInputInvalidCost(): void
    {
        $errors = ProductValidator::validateProductInput(
            'EAN123456789',
            'Producto Test',
            'invalido',          // costo INVÁLIDO (no es número)
            '100',
            '10'
        );
        $this->assertNotEmpty($errors); // ✅ Debe haber error
        $this->assertStringContainsString('número', strtolower($errors[0])); // ✅ El error debe mencionar "número"
    }

    /**
     * TEST 5: Verifica que costo NEGATIVO genera error
     * 
     * Entrada: Costo con valor negativo + otros campos válidos
     * Resultado esperado: Array con error que contiene "positivo"
     * 
     * Razón: El costo debe ser > 0 (positivo)
     */
    public function testProductInputNegativeCost(): void
    {
        $errors = ProductValidator::validateProductInput(
            'EAN123456789',
            'Producto Test',
            '-10.50',            // costo NEGATIVO
            '100',
            '10'
        );
        $this->assertNotEmpty($errors); // ✅ Debe haber error
        $this->assertStringContainsString('positivo', strtolower($errors[0])); // ✅ El error debe mencionar "positivo"
    }

    /**
     * TEST 6: Verifica que costo CERO genera error
     * 
     * Entrada: Costo con valor 0 + otros campos válidos
     * Resultado esperado: Array con al least 1 error
     * 
     * Razón: El costo debe ser > 0, no puede ser 0
     */
    public function testProductInputZeroCost(): void
    {
        $errors = ProductValidator::validateProductInput(
            'EAN123456789',
            'Producto Test',
            '0',                 // costo CERO
            '100',
            '10'
        );
        $this->assertNotEmpty($errors); // ✅ Debe haber error
    }

    /**
     * TEST 7: Verifica que cantidad INVÁLIDA (texto) genera error
     * 
     * Entrada: Cantidad con valor que NO es número + otros campos válidos
     * Resultado esperado: Array con al least 1 error
     */
    public function testProductInputInvalidQuantity(): void
    {
        $errors = ProductValidator::validateProductInput(
            'EAN123456789',
            'Producto Test',
            '25.50',
            'invalido',          // cantidad INVÁLIDA (no es número)
            '10'
        );
        $this->assertNotEmpty($errors); // ✅ Debe haber error
    }

    /**
     * TEST 8: Verifica que cantidad NEGATIVA genera error
     * 
     * Entrada: Cantidad con valor negativo + otros campos válidos
     * Resultado esperado: Array con error que contiene "no negativo"
     * 
     * Razón: La cantidad debe ser >= 0 (no puede ser negativa)
     */
    public function testProductInputNegativeQuantity(): void
    {
        $errors = ProductValidator::validateProductInput(
            'EAN123456789',
            'Producto Test',
            '25.50',
            '-5',                // cantidad NEGATIVA
            '10'
        );
        $this->assertNotEmpty($errors); // ✅ Debe haber error
        $this->assertStringContainsString('no negativo', strtolower($errors[0])); // ✅ El error debe mencionar "no negativo"
    }

    /**
     * TEST 9: Verifica que cantidad CERO es VÁLIDA
     * 
     * Entrada: Cantidad con valor 0 + otros campos válidos
     * Resultado esperado: Array vacío (SIN errores)
     * 
     * Razón: La cantidad puede ser 0 (producto sin stock inicial)
     */
    public function testProductInputZeroQuantity(): void
    {
        $errors = ProductValidator::validateProductInput(
            'EAN123456789',
            'Producto Test',
            '25.50',
            '0',                 // cantidad CERO (¡VÁLIDO!)
            '10'
        );
        $this->assertEmpty($errors); // ✅ Debe estar vacío - cero es válido
    }

    /**
     * TEST 10: Verifica que stock mínimo INVÁLIDO (texto) genera error
     * 
     * Entrada: Stock mínimo con valor que NO es número + otros campos válidos
     * Resultado esperado: Array con al least 1 error
     */
    public function testProductInputInvalidMinStock(): void
    {
        $errors = ProductValidator::validateProductInput(
            'EAN123456789',
            'Producto Test',
            '25.50',
            '100',
            'invalido'           // stock mínimo INVÁLIDO (no es número)
        );
        $this->assertNotEmpty($errors); // ✅ Debe haber error
    }

    /**
     * TEST 11: Verifica que estado INVÁLIDO genera error
     * 
     * Entrada: Estado con valor NO permitido (debe ser ACTIVO o INACTIVO) + otros campos válidos
     * Resultado esperado: Array con error que contiene "ACTIVO"
     * 
     * Razón: Solo se permiten dos estados: ACTIVO o INACTIVO
     */
    public function testProductInputInvalidStatus(): void
    {
        $errors = ProductValidator::validateProductInput(
            'EAN123456789',
            'Producto Test',
            '25.50',
            '100',
            '10',
            'Descripción',
            'INVALIDO'           // estado INVALIDO (no es ACTIVO ni INACTIVO)
        );
        $this->assertNotEmpty($errors); // ✅ Debe haber error
        $this->assertStringContainsString('ACTIVO', $errors[0]); // ✅ El error debe mencionar "ACTIVO"
    }

    /**
     * TEST 12: Verifica que descripción PRESENTE NO genera error
     * 
     * Entrada: Todos los campos obligatorios + descripción completa
     * Resultado esperado: Array vacío (sin errores)
     * 
     * Razón: La descripción es opcional pero puede estar presente
     */
    public function testProductInputWithDescription(): void
    {
        $errors = ProductValidator::validateProductInput(
            'EAN123456789',
            'Producto Test',
            '25.50',
            '100',
            '10',
            'Una descripción completa del producto'  // descripción PRESENTE
        );
        $this->assertEmpty($errors); // ✅ Debe estar vacío
    }

    /**
     * TEST 13: Verifica que descripción AUSENTE (NULL) NO genera error
     * 
     * Entrada: Todos los campos obligatorios + descripción NULL
     * Resultado esperado: Array vacío (sin errores)
     * 
     * Razón: La descripción es OPCIONAL
     */
    public function testProductInputWithoutDescription(): void
    {
        $errors = ProductValidator::validateProductInput(
            'EAN123456789',
            'Producto Test',
            '25.50',
            '100',
            '10',
            null                 // descripción NULL (opcional)
        );
        $this->assertEmpty($errors); // ✅ Debe estar vacío
    }
}