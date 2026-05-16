<?php
session_start();
require __DIR__ . '/db.php';
require __DIR__ . '/vendor/autoload.php';

use App\Database\ProductRepository;

// Proteger la página.
if (!isset($_SESSION['documento_empleado'])) {
    header('Location: ' . base_url('login.php'));
    exit;
}

$nombre_usuario = $_SESSION['nombre'] ?? 'Usuario';

$productos = [];
$error = '';

// --- CONSULTA ---
try {
    $productRepo = new ProductRepository($pdo);
    $productos = $productRepo->findAll();
} catch (PDOException $e) {
    $error = 'Error al obtener los productos: ' . $e->getMessage();
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listar Productos</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="dashboard-page">
    <aside class="sidebar">
        <h2 class="brand">CapsuleCorp</h2>
        <nav>
            <ul>
                <li><a href="dashboard.php">🏠 Dashboard</a></li>
                <li><a href="add-product.php">➕ Agregar producto</a></li>
                <li><a href="list-products.php" class="actvie">📋 Listar productos</a></li>
                <li><a href="add-provider.php">➕ Agregar proveedor</a></li>
                <li><a href="list-providers.php">🏭📋Listar proveedores</a></li>
            </ul>
        </nav>
    </aside>

    <main class="main-content">
        <header>
            <h1>Listado de Productos</h1>
            <a href="logout.php">
                <button class="btn-logout">Cerrar Sesión</button>
            </a>
        </header>

        <section class="content">
            <?php if ($error !== ''): ?>
                <div style="background-color:#f8d7da;color:#721c24;
                            padding:12px 16px;margin-bottom:20px;
                            border-radius:8px;border:1px solid #f5c6cb;
                            font-size:14px;text-align:center;">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Código de barras</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Costo unitario</th>
                        <th>Cantidad inicial</th>
                        <th>Stock mínimo</th>
                        <th>Fecha registro</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                    <tbody>
                        <?php if (empty($productos)): ?>
                            <tr>
                                <td colspan="9" style="text-align:center;">
                                    No hay productos registrados.
                                </td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($productos as $p): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($p['codigo_barras']); ?></td>
                                        <td><?php echo htmlspecialchars($p['nombre_producto']); ?></td>
                                        <td><?php echo htmlspecialchars($p['descripcion_producto'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($p['costo_unitario']); ?></td>
                                        <td><?php echo htmlspecialchars($p['cantidad_inicial']); ?></td>
                                        <td><?php echo htmlspecialchars($p['stock_minimo']); ?></td>
                                        <td><?php echo htmlspecialchars($p['fecha_registro_producto']); ?></td>
                                        <td><?php echo htmlspecialchars($p['estado_producto']); ?></td>
                                        <td>
                                            <div class="table-actions">
                                                <a href="edit-product.php?codigo_barras=<?php echo urlencode($p['codigo_barras']); ?>" style="text-decoration: none;">
                                                    <button class="btn-info">✏️ Editar</button>
                                                </a>
                                                <a href="delete-product.php?codigo_barras=<?php echo urlencode($p['codigo_barras']); ?>" style="text-decoration: none;">
                                                    <button class="btn-danger">🗑️ Eliminar</button>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </thead>
            </table>
            </div>
        </section>
    </main>
</body>
</html>