<?php
namespace App\Database;

class EmployeeRepository
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Busca un empleado por email
     */
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM empleado WHERE correo_empleado = ?");
        $stmt->execute([$email]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Busca un empleado por documento
     */
    public function findByDocumento(string $documento): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM empleado WHERE documento_empleado = ?");
        $stmt->execute([$documento]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Verifica si existe un empleado con documento o email
     */
    public function existsByDocumentoOrEmail(string $documento, string $email): bool
    {
        $stmt = $this->pdo->prepare("SELECT 1 FROM empleado WHERE documento_empleado = ? OR correo_empleado = ?");
        $stmt->execute([$documento, $email]);
        return (bool) $stmt->fetch();
    }

    /**
     * Crea un nuevo empleado
     */
    public function create(
        string $documento,
        string $nombres,
        string $apellidos,
        string $correo,
        string $contraHash,
        ?string $telefono = null
    ): bool {
        $stmt = $this->pdo->prepare(
            "INSERT INTO empleado (documento_empleado, nombres_empleado, apellidos_empleado, correo_empleado, contra_empleado, telefono_empleado)
            VALUES (?, ?, ?, ?, ?, ?)"
        );
        return $stmt->execute([$documento, $nombres, $apellidos, $correo, $contraHash, $telefono]);
    }

    /**
     * Obtiene todos los empleados
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM empleado ORDER BY nombres_empleado");
        $stmt->execute();
        return $stmt->fetchAll() ?: [];
    }
}