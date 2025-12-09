<?php
require __DIR__ . '/db.php'; // Usa la misma conexión PDO que en register.php.[file:4][file:5]

$mensaje = '';
$ok = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo_barras      = trim($_POST['codigo_barras']      ?? '');
    $nombre_producto    = trim($_POST['nombre_producto']    ?? '');
    $descripcion_producto = trim($_POST['descripcion_producto'] ?? '');
    $costo_unitario     = $_POST['costo_unitario']     ?? '';
    $cantidad_inicial   = $_POST['cantidad_inicial']   ?? '';
    $stock_minimo       = $_POST['stock_minimo']       ?? '';
    $estado_producto    = $_POST['estado_producto']    ?? 'ACTIVO';

    // Validación básica
    if (
        $codigo_barras === '' ||
        $nombre_producto === '' ||
        $costo_unitario === '' ||
        $cantidad_inicial === '' ||
        $stock_minimo === ''
    ) {
        $mensaje = 'Todos los campos con * son obligatorios.';
    } else {
        try {
            // Verificar si ya existe un producto con ese código de barras
            $stmt = $pdo->prepare(
                'SELECT 1 FROM producto WHERE codigo_barras = ?'
            );
            $stmt->execute([$codigo_barras]);

            if ($stmt->fetch()) {
                $mensaje = 'Ya existe un producto con ese código de barras.';
            } else {
                // Insertar el nuevo producto
                $ins = $pdo->prepare(
                    'INSERT INTO producto
                    (codigo_barras, nombre_producto, descripcion_producto, 
                    costo_unitario, cantidad_inicial, stock_minimo, estado_producto)
                    VALUES (?, ?, ?, ?, ?, ?, ?)'
                );

                $descripcion_param = $descripcion_producto !== '' ? $descripcion_producto : null;

                if ($ins->execute([
                    $codigo_barras,
                    $nombre_producto,
                    $descripcion_param,
                    $costo_unitario,
                    $cantidad_inicial,
                    $stock_minimo,
                    $estado_producto
                ])) {
                    $ok = 'Producto registrado correctamente.';
                    // Limpiar valores del formulario
                    $codigo_barras = $nombre_producto = $descripcion_producto =
                    $costo_unitario = $cantidad_inicial = $stock_minimo = '';
                } else {
                    $errorInfo = $ins->errorInfo();
                    $mensaje = 'Error al insertar el producto: ' . $errorInfo[2];
                }
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
    <title>Agregar Producto</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="dashboard-page">
    <aside class="sidebar">
        <h2 class="brand">CapsuleCorp</h2>
        <nav>
            <ul>
                <li><a href="dashboard.php">🏠 Dashboard</a></li>
                <li><a href="add-product.php" class="active">➕ Agregar producto</a></li>
                <li><a href="list-products.php">📋 Listar productos</a></li>
                <li><a href="add-provider.php">➕ Agregar proveedor</a></li>
                <li><a href="list-providers.php">🏭📋Listar proveedores</a></li>
            </ul>
        </nav>
    </aside>

    <main class="main-content">
        <header>
            <h1>Agregar Nuevo Producto</h1>
            <a href="index.php">
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
                    <label for="codigo_barras">Código de barras </label><label id="asterico">*</label>
                    <input class="form-data" type="text" id="codigo_barras" name="codigo_barras"
                        value="<?php echo htmlspecialchars($codigo_barras ?? ''); ?>"
                        required
                    >

                    <label for="nombre_producto">Nombre del producto </label><label id="asterico">*</label>
                    <input class="form-data" type="text" id="nombre_producto" name="nombre_producto"
                        value="<?php echo htmlspecialchars($nombre_producto ?? ''); ?>"
                        required
                    >

                    <label for="descripcion_producto">Descripción</label>
                    <textarea class="form-data" id="descripcion_producto" name="descripcion_producto" rows="3"
                    ><?php echo htmlspecialchars($descripcion_producto ?? ''); ?></textarea>

                    <label for="costo_unitario">Costo unitario </label><label id="asterico">*</label>
                    <input class="form-data" type="number" step="0.01" min="0" id="costo_unitario" name="costo_unitario"
                        value="<?php echo htmlspecialchars($costo_unitario ?? ''); ?>"
                        required
                    >

                    <label for="cantidad_inicial">Cantidad inicial </label><label id="asterico">*</label>
                    <input class="form-data" type="number" min="0" id="cantidad_inicial" name="cantidad_inicial"
                        value="<?php echo htmlspecialchars($cantidad_inicial ?? ''); ?>"
                        required
                    >

                    <label for="stock_minimo">Stock mínimo </label><label id="asterico">*</label>
                    <input class="form-data" type="number" min="0"
                        id="stock_minimo" name="stock_minimo"
                        value="<?php echo htmlspecialchars($stock_minimo ?? ''); ?>"
                        required
                    >

                    <label for="estado_producto">Estado</label>
                    <select class="form-data" id="estado_producto" name="estado_producto"
                    >
                        <option value="ACTIVO"   <?php echo ($estado_producto ?? '') === 'ACTIVO'   ? 'selected' : ''; ?>>ACTIVO</option>
                        <option value="INACTIVO" <?php echo ($estado_producto ?? '') === 'INACTIVO' ? 'selected' : ''; ?>>INACTIVO</option>
                    </select>

                    <button type="submit" class="btn-primary">Guardar</button>
                </form>
        </section>
    </main>
</body>
</html>
