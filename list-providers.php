<?php
session_start();
require __DIR__ . '/db.php';
require __DIR__ . '/vendor/autoload.php';

use App\Database\ProviderRepository;

if (!isset($_SESSION['documento_empleado'])) {
    header('Location: ' . base_url('login.php'));
    exit;
}

$proveedores = [];
$error = '';

try {
    $providerRepo = new ProviderRepository($pdo);
    $proveedores = $providerRepo->findAll();
} catch (PDOException $e) {
    $error = 'Error al obtener los proveedores: ' . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listar Proveedores</title>
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
                <li><a href="list-providers.php" class="active">🏭📋Listar proveedores</a></li>
            </ul>
        </nav>
    </aside>

    <main class="main-content">
        <header>
            <h1>Listado de Proveedores</h1>
            <a href="logout.php"><button class="btn-logout">Cerrar Sesión</button></a>
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
                        <th>ID</th>
                        <th>Empresa</th>
                        <th>Representante</th>
                        <th>Correo</th>
                        <th>Teléfono</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($proveedores)): ?>
                        <tr>
                            <td colspan="7" style="text-align:center;">
                                No hay proveedores registrados.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($proveedores as $prov): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($prov['id_proveedor']); ?></td>
                                <td><?php echo htmlspecialchars($prov['empresa']); ?></td>
                                <td><?php echo htmlspecialchars($prov['representante']); ?></td>
                                <td><?php echo htmlspecialchars($prov['correo_proveedor']); ?></td>
                                <td><?php echo htmlspecialchars($prov['telefono_proveedor'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($prov['estado_proveedor']); ?></td>
                                <td>
                                    <div class="table-actions">
                                        <a href="edit-provider.php?id=<?php echo urlencode($prov['id_proveedor']); ?>" style="text-decoration: none;">
                                            <button class="btn-info">✏️ Editar</button>
                                        </a>
                                        <a href="delete-provider.php?id=<?php echo urlencode($prov['id_proveedor']); ?>" style="text-decoration: none;">
                                            <button class="btn-danger">🗑️ Eliminar</button>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            </div>
        </section>
    </main>
</body>
</html>