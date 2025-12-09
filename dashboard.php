<?php
session_start();
require __DIR__ . '/db.php';

if (!isset($_SESSION['documento_empleado'])) {
  header('Location: ' . base_url('login.php'));
  exit;
}

$nombre = $_SESSION['nombre'] ?? 'Usuario';
?>

<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard – Sistema de Inventarios</title>
    <link rel="stylesheet" href="assets/css/style.css"/>
  </head>
  <body class="dashboard-page">
    <aside class="sidebar">
      <h2 class="brand">CapsuleCorp</h2>
      <nav>
        <ul>
          <li><a href="add-product.php">➕ Agregar producto</a></li>
          <li><a href="list-products.php">📋 Listar productos</a></li>
          <li><a href="add-provider.php">➕ Agregar proveedor</a></li>
          <li><a href="list-providers.php">🏭📋Listar proveedores</a></li>
          <!-- aquí más opciones -->
        </ul>
      </nav>
    </aside>

    <main class="main-content">
      <header>
        <h1>Bienvenido</h1>
        <a href="login.php">
          <button class="btn-logout">Cerrar Sesión</button>
        </a>
      </header>
      <section class="content">
        <!-- Contenido dinámico según la sección -->
        <p>Selecciona una opción del menú.</p>
      </section>
    </main>
  </body>
</html>
