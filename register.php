<?php
require __DIR__ . '/db.php';
require __DIR__ . '/vendor/autoload.php';

use App\Auth\PasswordHandler;
use App\Database\EmployeeRepository;
use App\Validators\AuthValidator;

$mensaje = '';
$ok = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $documento = trim($_POST['documento_empleado'] ?? '');
  $nombres   = trim($_POST['nombres_empleado'] ?? '');
  $apellidos = trim($_POST['apellidos_empleado'] ?? '');
  $correo    = trim($_POST['correo_empleado'] ?? '');
  $contra    = $_POST['contra_empleado'] ?? '';
  $telefono  = trim($_POST['telefono_empleado'] ?? '');

  $telefono = ($telefono === '') ? null : $telefono;

  // Usar el validador
  $errors = AuthValidator::validateRegisterInput($documento, $nombres, $apellidos, $correo, $contra, $telefono);

  if (!empty($errors)) {
    $mensaje = $errors[0];
  } else {
    try {
      $employeeRepo = new EmployeeRepository($pdo);

      // Verificar duplicados
      if ($employeeRepo->existsByDocumentoOrEmail($documento, $correo)) {
        $mensaje = 'El documento o correo ya existe en el sistema.';
      } else {
        // Encriptar contraseña usando el password handler
        $hash = PasswordHandler::hash($contra);

        // Crear el empleado
        if ($employeeRepo->create($documento, $nombres, $apellidos, $correo, $hash, $telefono)) {
          $ok = 'Empleado registrado correctamente. Redirigiendo al login...';
          header('Refresh: 2; URL=' . base_url('login.php'));
          exit;
        } else {
          $mensaje = 'Error al insertar el empleado.';
        }
      }
    } catch (PDOException $e) {
      $mensaje = 'Error de base de datos: ' . $e->getMessage();
    } catch (Exception $e) {
      $mensaje = 'Error general: ' . $e->getMessage();
    }
  }
}
?>

<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Registrarse - Gestión de Inventarios</title>
    <link rel="stylesheet" href="assets/css/style.css"/>
  </head>
  <body class="login-page">
    <div class="login-container">
      <h1>Registrarse</h1>

      <?php if (!empty($ok)): ?>
        <div style="background-color: #d4edda; color: #155724; padding: 12px 16px; margin-bottom: 20px; border-radius: 8px; border: 1px solid #c3e6cb; font-size: 14px; text-align: center;">
          <?= htmlspecialchars($ok) ?></div>
      <?php endif; ?>

      <?php if (!empty($mensaje)): ?>
        <div style="background-color: #f8d7da; color: #721c24; padding: 12px 16px; margin-bottom: 20px; border-radius: 8px; border: 1px solid #f5c6cb; font-size: 14px; text-align: center;">
          <?= htmlspecialchars($mensaje) ?></div>
      <?php endif; ?>

      <form action="" method="POST">
        <label for="documento">Documento </label><label id="asterico">*</label>
        <input type="text" id="documento" name="documento_empleado" placeholder="Ej: 12345678" required/>

        <label for="nombres">Nombres </label><label id="asterico">*</label>
        <input type="text" id="nombres" name="nombres_empleado" placeholder="Ej: Juan" required/>

        <label for="apellidos">Apellidos </label><label id="asterico">*</label>
        <input type="text" id="apellidos" name="apellidos_empleado" placeholder="Ej: Pérez" required/>

        <label for="correo">Correo </label><label id="asterico">*</label>
        <input type="email" id="correo" name="correo_empleado" placeholder="Ej: juan@email.com" required/>

        <label for="telefono">Teléfono</label>
        <input type="text" id="telefono" name="telefono_empleado" placeholder="Ej: 3001234567"/>

        <label for="password">Contraseña </label><label id="asterico">*</label>
        <input type="password" id="password" name="contra_empleado" placeholder="••••••••" required/>

        <button type="submit" class="btn-primary">Registrarse</button>
      </form>
      <a class="register" href="login.php">Ya tengo cuenta - Iniciar Sesión</a>
    </div>
  </body>
</html>