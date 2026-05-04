<?php
namespace Tests\Unit\Validators;

use App\Validators\AuthValidator;
use PHPUnit\Framework\TestCase;

class AuthValidatorTest extends TestCase
{
    /**
     * TEST ''1: Verifica que datos válidos NO generan errores de login
     * 
     * Entrada: email válido + contraseña
     * Resultado esperado: Array vacío (sin errores)
     */
    public function testValidLoginInput(): void
    {
        $errors = AuthValidator::validateLoginInput('usuario@example.com', 'password123');
        $this->assertEmpty($errors); // ✅ Debe estar vacío
    }
    
    /**
     * TEST 2: Verifica que email vacío genera error de login
     * 
     * Entrada: email vacío + contraseña válida
     * Resultado esperado: Array con 1 error que contiene la palabra "email"
     */
    public function testLoginInputEmptyEmail(): void
    {
        $errors = AuthValidator::validateLoginInput('', 'password123');
        $this->assertNotEmpty($errors); // ✅ Debe haber error
        $this->assertStringContainsString('email', strtolower($errors[0])); // ✅ El error debe mencionar "email"
    }

    /**
     * TEST 3: Verifica que contraseña vacía genera error de login
     * 
     * Entrada: email válido + contraseña vacía
     * Resultado esperado: Array con 1 error que contiene la palabra "contraseña"
     */
    public function testLoginInputEmptyPassword(): void
    {
        $errors = AuthValidator::validateLoginInput('usuario@example.com', '');
        $this->assertNotEmpty($errors); // ✅ Debe haber error
        $this->assertStringContainsString('contraseña', strtolower($errors[0])); // ✅ El error debe mencionar "contraseña"
    }

    /**
     * TEST 4: Verifica que email Y contraseña vacíos generan 2 errores
     * 
     * Entrada: email vacío + contraseña vacía
     * Resultado esperado: Array con exactamente 2 errores
     */
    public function testLoginInputBothEmpty(): void
    {
        $errors = AuthValidator::validateLoginInput('', '');
        $this->assertCount(2, $errors); // ✅ Debe haber exactamente 2 errores
    }

    /**
     * TEST 5: Verifica que registro con datos VÁLIDOS NO genera errores
     * 
     * Entrada: Todos los campos obligatorios válidos (documento, nombres, apellidos, correo, contraseña)
     * Resultado esperado: Array vacío (sin errores)
     */
    public function testValidRegisterInput(): void
    {
        $errors = AuthValidator::validateRegisterInput(
            '12345678',          // documento válido
            'Juan',              // nombres válido
            'Pérez',             // apellidos válido
            'juan@example.com',  // correo válido
            'password123'        // contraseña válida (6+ caracteres)
        );
        $this->assertEmpty($errors); // ✅ Debe estar vacío
    }

    /**
     * TEST 6: Verifica que documento vacío genera error en registro
     * 
     * Entrada: Documento vacío + otros campos válidos
     * Resultado esperado: Array con al menos 1 error
     */
    public function testRegisterInputEmptyDocumento(): void
    {
        $errors = AuthValidator::validateRegisterInput(
            '',                  // documento VACÍO
            'Juan',
            'Pérez',
            'juan@example.com',
            'password123'
        );
        $this->assertNotEmpty($errors); // ✅ Debe haber error
    }

    /**
     * TEST 7: Verifica que correo vacío genera error en registro
     * 
     * Entrada: Correo vacío + otros campos válidos
     * Resultado esperado: Array con al menos 1 error
     */
    public function testRegisterInputEmptyEmail(): void
    {
        $errors = AuthValidator::validateRegisterInput(
            '12345678',
            'Juan',
            'Pérez',
            '',                  // correo VACÍO
            'password123'
        );
        $this->assertNotEmpty($errors); // ✅ Debe haber error
    }

    /**
     * TEST 8: Verifica que correo INVÁLIDO genera error en registro
     * 
     * Entrada: Correo sin formato válido (sin @) + otros campos válidos
     * Resultado esperado: Array con error que contiene "no es válido"
     */
    public function testRegisterInputInvalidEmail(): void
    {
        $errors = AuthValidator::validateRegisterInput(
            '12345678',
            'Juan',
            'Pérez',
            'notanemail',         // correo INVÁLIDO (sin @)
            'password123'
        );
        $this->assertNotEmpty($errors); // ✅ Debe haber error
        $this->assertStringContainsString('no es válido', $errors[0]); // ✅ El error debe decir "no es válido"
    }

    /**
     * TEST 9: Verifica que contraseña CORTA (<6 caracteres) genera error
     * 
     * Entrada: Contraseña con solo 4 caracteres + otros campos válidos
     * Resultado esperado: Array con error que contiene "6" (debe tener 6+ caracteres)
     */
    public function testRegisterInputShortPassword(): void
    {
        $errors = AuthValidator::validateRegisterInput(
            '12345678',
            'Juan',
            'Pérez',
            'juan@example.com',
            'pass'               // contraseña CORTA (solo 4 caracteres)
        );
        $this->assertNotEmpty($errors); // ✅ Debe haber error
        $this->assertStringContainsString('6', $errors[0]); // ✅ El error debe mencionar "6" (mínimo de caracteres)
    }

    /**
     * TEST 10: Verifica que contraseña vacía genera error en registro
     * 
     * Entrada: Contraseña vacía + otros campos válidos
     * Resultado esperado: Array con al menos 1 error
     */
    public function testRegisterInputEmptyPassword(): void
    {
        $errors = AuthValidator::validateRegisterInput(
            '12345678',
            'Juan',
            'Pérez',
            'juan@example.com',
            ''                   // contraseña VACÍA
        );
        $this->assertNotEmpty($errors); // ✅ Debe haber error
    }

    /**
     * TEST 11: Verifica que TODOS los campos vacíos generan TODOS los errores
     * 
     * Entrada: Todos los campos vacíos
     * Resultado esperado: Array con exactamente 5 errores (uno por cada campo obligatorio)
     * 
     * Los campos obligatorios son: documento, nombres, apellidos, correo, contraseña
     */
    public function testRegisterInputAllFieldsEmpty(): void
    {
        $errors = AuthValidator::validateRegisterInput('', '', '', '', '');
        $this->assertCount(5, $errors); // ✅ Debe haber exactamente 5 errores (uno por cada campo)
    }
}