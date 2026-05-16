<?php
namespace App\Database;

class ProviderRepository
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Busca un proveedor por email
     */
    public function findByEmail(string $correo_proveedor): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM proveedor WHERE correo_proveedor = ?");
        $stmt->execute([$correo_proveedor]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Busca un proveedor por ID
     */
    public function findById(int $id_proveedor): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM proveedor WHERE id_proveedor = ?");
        $stmt->execute([$id_proveedor]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Verifica si existe un proveedor con ese email
     */
    public function existsByEmail(string $correo_proveedor): bool
    {
        $stmt = $this->pdo->prepare("SELECT 1 FROM proveedor WHERE correo_proveedor = ?");
        $stmt->execute([$correo_proveedor]);
        return (bool) $stmt->fetch();
    }

    /**
     * Crea un nuevo proveedor
     * Note: id_proveedor is AUTO_INCREMENT, not passed as parameter
     */
    public function create(
        string $empresa,
        string $representante,
        string $correo_proveedor,
        ?string $telefono_proveedor = null,
        string $estado_proveedor = 'ACTIVO'
    ): bool {
        $stmt = $this->pdo->prepare(
            "INSERT INTO proveedor (empresa, representante, correo_proveedor, telefono_proveedor, estado_proveedor)
            VALUES (?, ?, ?, ?, ?)"
        );
        return $stmt->execute([
            $empresa,
            $representante,
            $correo_proveedor,
            $telefono_proveedor,
            $estado_proveedor
        ]);
    }

    /**
     * Obtiene todos los proveedores
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM proveedor ORDER BY empresa");
        $stmt->execute();
        return $stmt->fetchAll() ?: [];
    }

    /**
     * Actualiza un proveedor
     */
    public function update(
        int $id_proveedor,
        string $empresa,
        string $representante,
        string $correo_proveedor,
        ?string $telefono_proveedor = null,
        string $estado_proveedor = 'ACTIVO'
    ): bool {
        $stmt = $this->pdo->prepare(
            "UPDATE proveedor SET empresa = ?, representante = ?, correo_proveedor = ?, telefono_proveedor = ?, estado_proveedor = ? WHERE id_proveedor = ?"
        );
        return $stmt->execute([
            $empresa,
            $representante,
            $correo_proveedor,
            $telefono_proveedor,
            $estado_proveedor,
            $id_proveedor
        ]);
    }

    /**
     * Elimina un proveedor por ID
     */
    public function delete(int $id_proveedor): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM proveedor WHERE id_proveedor = ?");
        return $stmt->execute([$id_proveedor]);
    }
}