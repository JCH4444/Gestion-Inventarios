<?php
namespace Tests\Unit\Auth;

use App\Auth\PasswordHandler;
use PHPUnit\Framework\TestCase;

class PasswordHandlerTest extends TestCase
{
    public function testHashPassword(): void
    {
        $password = 'miContraseña123';
        $hash = PasswordHandler::hash($password);
        
        // El hash no debe ser igual al password
        $this->assertNotEquals($password, $hash);
        
        // El hash debe ser una cadena válida
        $this->assertIsString($hash);
        $this->assertNotEmpty($hash);
    }

    public function testVerifyPassword(): void
    {
        $password = 'miContraseña123';
        $hash = PasswordHandler::hash($password);
        
        // El password debe verificarse correctamente
        $this->assertTrue(PasswordHandler::verify($password, $hash));
    }

    public function testVerifyPasswordFails(): void
    {
        $password = 'miContraseña123';
        $wrongPassword = 'contraseñaIncorrecta';
        $hash = PasswordHandler::hash($password);
        
        // El password incorrecto no debe verificarse
        $this->assertFalse(PasswordHandler::verify($wrongPassword, $hash));
    }

    public function testHashDifferentPasswords(): void
    {
        $password1 = 'password123';
        $password2 = 'password123';
        
        $hash1 = PasswordHandler::hash($password1);
        $hash2 = PasswordHandler::hash($password2);
        
        // Hashes diferentes incluso para mismo password (BCRYPT es aleatorio)
        $this->assertNotEquals($hash1, $hash2);
        
        // Pero ambos deben verificarse con sus respectivos passwords
        $this->assertTrue(PasswordHandler::verify($password1, $hash1));
        $this->assertTrue(PasswordHandler::verify($password2, $hash2));
    }
}