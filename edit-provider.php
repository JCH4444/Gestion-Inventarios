<?php
session_start();
require __DIR__ . '/db.php';
require __DIR__ . '/vendor/autoload.php';

use App\Database\ProviderRepository;
use App\Validators\ProviderValidator;

if (!isset($_SESSION['documento_empleado'])) {
    header('Location: ' . base_url('login.php'));
    exit;
}

$mensaje = '';
$ok = '';
$id_proveedor = $_GET['id'] ?? '';

if (!$id_proveedor) {
    header('Location: ' . base_url('list-providers.php'));
    exit;
}

$proveedor = null;
$empresa = '';
$representante = '';
$correo_proveedor = '';
$telefono_proveedor = '';
$estado_proveedor = 'ACTIVO';

try {
    $providerRepo = new ProviderRepository($pdo);
    $proveedor = $providerRepo->findById((int)$id_proveedor);
    
    if (!$proveedor) {
        header('Location: ' . base_url('list-providers.php'));
        exit;
    }
    
    // Pre-llenar valores
    $empresa = $proveedor['empresa'];
    $representante = $proveedor['representante'];
    $correo_proveedor = $proveedor['correo_proveedor'];
    $telefono_proveedor = $proveedor['telefono_proveedor'] ?? '';
    $estado_proveedor = $proveedor['estado_proveedor'];
} catch (PDOException $e) {
    $mensaje = 'Error al obtener el proveedor: ' . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $empresa = trim($_POST['empresa'] ?? '');
    $representante = trim($_POST['representante'] ?? '');
    $correo_proveedor = trim($_POST['correo_proveedor'] ?? '');
    $telefono_proveedor = trim($_POST['telefono_proveedor'] ?? '');
    $estado_proveedor = $_POST['estado_proveedor'] ?? 'ACTIVO';

    $errors = ProviderValidator::validateProviderInput(
        $empresa,
        $representante,
        $correo_proveedor,
        $telefono_proveedor !== '' ? $telefono_proveedor : null,
        $estado_proveedor
    );

    if (!empty($errors)) {
        $mensaje = $errors[0];
    } else {
        try {
            $providerRepo = new ProviderRepository($pdo);
            if ($providerRepo->update(
                (int)$id_proveedor,
                $empresa,
                $representante,
                $correo_proveedor,
                $telefono_proveedor !== '' ? $telefono_proveedor : null,
                $estado_proveedor
            )) {
                $ok = 'Proveedor actualizado correctamente.';
            } else {
                $mensaje = 'Error al actualizar el proveedor.';
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
    <title>Editar Proveedor</title>
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
            <h1>Editar Proveedor</h1>
            <a href="logout.php"><button class="btn-logout">Cerrar Sesión</button></a>
        </header>

        <section class="content">
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
                <label for="empresa">Empresa </label><label id="asterico">*</label>
                <input
                    class="form-data"
                    type="text"
                    id="empresa"
                    name="empresa"
                    value="<?php echo htmlspecialchars($empresa); ?>"
                    required
                >

                <label for="representante">Representante </label><label id="asterico">*</label>
                <input
                    class="form-data"
                    type="text"
                    id="representante"
                    name="representante"
                    value="<?php echo htmlspecialchars($representante); ?>"
                    required
                >

                <label for="correo_proveedor">Correo proveedor </label><label id="asterico">*</label>
                <input
                    class="form-data"
                    type="email"
                    id="correo_proveedor"
                    name="correo_proveedor"
                    value="<?php echo htmlspecialchars($correo_proveedor); ?>"
                    required
                >

                <label for="telefono_proveedor">Teléfono</label>
                <input
                    class="form-data"
                    type="text"
                    id="telefono_proveedor"
                    name="telefono_proveedor"
                    value="<?php echo htmlspecialchars($telefono_proveedor); ?>"
                >

                <label for="estado_proveedor">Estado</label>
                <select
                    class="form-data"
                    id="estado_proveedor"
                    name="estado_proveedor"
                >
                    <option value="ACTIVO"   <?php echo $estado_proveedor === 'ACTIVO'   ? 'selected' : ''; ?>>ACTIVO</option>
                    <option value="INACTIVO" <?php echo $estado_proveedor === 'INACTIVO' ? 'selected' : ''; ?>>INACTIVO</option>
                </select>

                <button type="submit" class="btn-primary">Actualizar</button>
                <a href="list-providers.php" style="text-decoration: none;">
                    <button type="button" class="btn-secondary">Cancelar</button>
                </a>
            </form>
        </section>
    </main>
</body>
</html>
