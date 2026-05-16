<?php
namespace App\Database;

class ProductRepository
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Busca un producto por código de barras
     */
    public function findByBarcode(string $codigo_barras): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM producto WHERE codigo_barras = ?");
        $stmt->execute([$codigo_barras]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Verifica si existe un producto con ese código de barras
     */
    public function existsByBarcode(string $codigo_barras): bool
    {
        $stmt = $this->pdo->prepare("SELECT 1 FROM producto WHERE codigo_barras = ?");
        $stmt->execute([$codigo_barras]);
        return (bool) $stmt->fetch();
    }

    /**
     * Crea un nuevo producto
     */
    public function create(
        string $codigo_barras,
        string $nombre_producto,
        ?string $descripcion_producto,
        float $costo_unitario,
        int $cantidad_inicial,
        int $stock_minimo,
        string $estado_producto = 'ACTIVO'
    ): bool {
        $stmt = $this->pdo->prepare(
            "INSERT INTO producto
            (codigo_barras, nombre_producto, descripcion_producto, costo_unitario, cantidad_inicial, stock_minimo, estado_producto)
            VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        return $stmt->execute([
            $codigo_barras,
            $nombre_producto,
            $descripcion_producto,
            $costo_unitario,
            $cantidad_inicial,
            $stock_minimo,
            $estado_producto
        ]);
    }

    /**
     * Obtiene todos los productos
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM producto ORDER BY nombre_producto");
        $stmt->execute();
        return $stmt->fetchAll() ?: [];
    }

    /**
     * Actualiza un producto
     */
    public function update(
        string $codigo_barras,
        string $nombre_producto,
        ?string $descripcion_producto,
        float $costo_unitario,
        int $stock_minimo,
        string $estado_producto = 'ACTIVO'
    ): bool {
        $stmt = $this->pdo->prepare(
            "UPDATE producto SET nombre_producto = ?, descripcion_producto = ?, costo_unitario = ?, stock_minimo = ?, estado_producto = ? WHERE codigo_barras = ?"
        );
        return $stmt->execute([
            $nombre_producto,
            $descripcion_producto,
            $costo_unitario,
            $stock_minimo,
            $estado_producto,
            $codigo_barras
        ]);
    }

    /**
     * Elimina un producto por código de barras
     */
    public function delete(string $codigo_barras): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM producto WHERE codigo_barras = ?");
        return $stmt->execute([$codigo_barras]);
    }
}