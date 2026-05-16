<?php
session_start();
require __DIR__ . '/db.php';
require __DIR__ . '/vendor/autoload.php';

use App\Database\ProductRepository;
use App\Validators\ProductValidator;

if (!isset($_SESSION['documento_empleado'])) {
    header('Location: ' . base_url('login.php'));
    exit;
}

$mensaje = '';
$ok = '';
$codigo_barras = $_GET['codigo_barras'] ?? '';

if (!$codigo_barras) {
    header('Location: ' . base_url('list-products.php'));
    exit;
}

$producto = null;
$nombre_producto = '';
$descripcion_producto = '';
$costo_unitario = '';
$stock_minimo = '';
$estado_producto = 'ACTIVO';

try {
    $productRepo = new ProductRepository($pdo);
    $producto = $productRepo->findByBarcode($codigo_barras);
    
    if (!$producto) {
        header('Location: ' . base_url('list-products.php'));
        exit;
    }
    
    // Pre-llenar valores
    $nombre_producto = $producto['nombre_producto'];
    $descripcion_producto = $producto['descripcion_producto'] ?? '';
    $costo_unitario = $producto['costo_unitario'];
    $stock_minimo = $producto['stock_minimo'];
    $estado_producto = $producto['estado_producto'];
} catch (PDOException $e) {
    $mensaje = 'Error al obtener el producto: ' . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_producto = trim($_POST['nombre_producto'] ?? '');
    $descripcion_producto = trim($_POST['descripcion_producto'] ?? '');
    $costo_unitario = $_POST['costo_unitario'] ?? '';
    $stock_minimo = $_POST['stock_minimo'] ?? '';
    $estado_producto = $_POST['estado_producto'] ?? 'ACTIVO';

    $errors = ProductValidator::validateProductInput(
        $codigo_barras,
        $nombre_producto,
        $costo_unitario,
        0, // cantidad_inicial no se usa en update
        $stock_minimo,
        $descripcion_producto !== '' ? $descripcion_producto : null,
        $estado_producto
    );

    if (!empty($errors)) {
        $mensaje = $errors[0];
    } else {
        try {
            $productRepo = new ProductRepository($pdo);
            if ($productRepo->update(
                $codigo_barras,
                $nombre_producto,
                $descripcion_producto !== '' ? $descripcion_producto : null,
                (float)$costo_unitario,
                (int)$stock_minimo,
                $estado_producto
            )) {
                $ok = 'Producto actualizado correctamente.';
            } else {
                $mensaje = 'Error al actualizar el producto.';
            }
        } catch (PDOException $e) {
            $mensaje = 'Error de base de datos: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="dashboard-page">
    <aside class="sidebar">
        <h2 class="brand">CapsuleCorp</h2>
        <nav>
            <ul>
                <li><a href="dashboard.php">🏠 Dashboard</a></li>
                <li><a href="add-product.php">➕ Agregar producto</a></li>
                <li><a href="list-products.php">📋 Listar productos</a></li>
                <li><a href="add-provider.php">➕ Agregar proveedor</a></li>
                <li><a href="list-providers.php">🏭📋Listar proveedores</a></li>
            </ul>
        </nav>
    </aside>

    <main class="main-content">
        <header>
            <h1>Editar Producto</h1>
            <a href="logout.php">
                <button class="btn-logout">Cerrar Sesión</button>
            </a>
        </header>

        <section class="content">
            <div class="new-product">
                <?php if ($mensaje !== ''): ?>
                    <div style="background-color:#f8d7da;color:#721c24;
                                padding:12px 16px;margin-bottom:20px;
                                border-radius:8px;border:1px solid #f5c6cb;
                                font-size:14px;text-align:center;">
                        <?php echo htmlspecialchars($mensaje); ?>
                    </div>
                <?php endif; ?>

                <?php if ($ok !== ''): ?>
                    <div style="background-color:#d4edda;color:#155724;
                                padding:12px 16px;margin-bottom:20px;
                                border-radius:8px;border:1px solid #c3e6cb;
                                font-size:14px;text-align:center;">
                        <?php echo htmlspecialchars($ok); ?>
                    </div>
                <?php endif; ?>

                <form class="form-card" method="POST" action="">
                    <label for="codigo_barras">Código de barras</label>
                    <input class="form-data" type="text" id="codigo_barras" name="codigo_barras"
                        value="<?php echo htmlspecialchars($codigo_barras); ?>"
                        disabled>

                    <label for="nombre_producto">Nombre del producto </label><label id="asterico">*</label>
                    <input class="form-data" type="text" id="nombre_producto" name="nombre_producto"
                        value="<?php echo htmlspecialchars($nombre_producto ?? ''); ?>"
                        required>

                    <label for="descripcion_producto">Descripción</label>
                    <textarea class="form-data" id="descripcion_producto" name="descripcion_producto" rows="3"
                    ><?php echo htmlspecialchars($descripcion_producto ?? ''); ?></textarea>

                    <label for="costo_unitario">Costo unitario </label><label id="asterico">*</label>
                    <input class="form-data" type="number" step="0.01" min="0" id="costo_unitario" name="costo_unitario"
                        value="<?php echo htmlspecialchars($costo_unitario ?? ''); ?>"
                        required>

                    <label for="stock_minimo">Stock mínimo </label><label id="asterico">*</label>
                    <input class="form-data" type="number" min="0"
                        id="stock_minimo" name="stock_minimo"
                        value="<?php echo htmlspecialchars($stock_minimo ?? ''); ?>"
                        required>

                    <label for="estado_producto">Estado</label>
                    <select class="form-data" id="estado_producto" name="estado_producto">
                        <option value="ACTIVO"   <?php echo ($estado_producto ?? '') === 'ACTIVO'   ? 'selected' : ''; ?>>ACTIVO</option>
                        <option value="INACTIVO" <?php echo ($estado_producto ?? '') === 'INACTIVO' ? 'selected' : ''; ?>>INACTIVO</option>
                    </select>

                    <button type="submit" class="btn-primary">Actualizar</button>
                    <a href="list-products.php" style="text-decoration: none;">
                        <button type="button" class="btn-secondary">Cancelar</button>
                    </a>
                </form>
        </section>
    </main>
</body>
</html>
