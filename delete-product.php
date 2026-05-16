<?php
session_start();
require __DIR__ . '/db.php';
require __DIR__ . '/vendor/autoload.php';

use App\Database\ProductRepository;

if (!isset($_SESSION['documento_empleado'])) {
    header('Location: ' . base_url('login.php'));
    exit;
}

$codigo_barras = $_GET['codigo_barras'] ?? '';

if (!$codigo_barras) {
    header('Location: ' . base_url('list-products.php'));
    exit;
}

try {
    $productRepo = new ProductRepository($pdo);
    $producto = $productRepo->findByBarcode($codigo_barras);
    
    if (!$producto) {
        header('Location: ' . base_url('list-products.php'));
        exit;
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['confirmar']) && $_POST['confirmar'] === 'si') {
            if ($productRepo->delete($codigo_barras)) {
                header('Location: ' . base_url('list-products.php?mensaje=Producto eliminado correctamente'));
                exit;
            } else {
                $error = 'Error al eliminar el producto.';
            }
        } else {
            header('Location: ' . base_url('list-products.php'));
            exit;
        }
    }
} catch (PDOException $e) {
    $error = 'Error de base de datos: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmar Eliminación</title>
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
            <h1>Confirmar Eliminación</h1>
            <a href="logout.php">
                <button class="btn-logout">Cerrar Sesión</button>
            </a>
        </header>

        <section class="content">
            <?php if (isset($error)): ?>
                <div style="background-color:#f8d7da;color:#721c24;
                            padding:12px 16px;margin-bottom:20px;
                            border-radius:8px;border:1px solid #f5c6cb;
                            font-size:14px;text-align:center;">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <div class="form-card" style="text-align: center;">
                <h2>¿Está seguro de que desea eliminar este producto?</h2>
                <p style="margin: 20px 0; color: #666;">
                    <strong>Código de barras:</strong> <?php echo htmlspecialchars($producto['codigo_barras']); ?><br>
                    <strong>Nombre:</strong> <?php echo htmlspecialchars($producto['nombre_producto']); ?>
                </p>
                <p style="color: #dc3545; font-weight: 600; margin-bottom: 20px;">
                    ⚠️ Esta acción no se puede deshacer.
                </p>

                <form method="POST" action="">
                    <button type="submit" name="confirmar" value="si" class="btn-danger">Eliminar</button>
                    <a href="list-products.php" style="text-decoration: none;">
                        <button type="button" class="btn-secondary">Cancelar</button>
                    </a>
                </form>
            </div>
        </section>
    </main>
</body>
</html>
