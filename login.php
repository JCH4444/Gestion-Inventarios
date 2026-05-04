<?php
session_start();
require __DIR__ . '/db.php';
require __DIR__ . '/vendor/autoload.php';

use App\Auth\PasswordHandler;
use App\Database\EmployeeRepository;

$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['correo_empleado'] ?? '');
  $password = $_POST['contra_empleado'] ?? '';

  // Usar el nuevo validador
  $errors = \App\Validators\AuthValidator::validateLoginInput($email, $password);
  
  if (!empty($errors)) {
    $mensaje = $errors[0];
  } else {
    try {
      // Usar el repositorio
      $employeeRepo = new EmployeeRepository($pdo);
      $user = $employeeRepo->findByEmail($email);

      // Usar el password handler
      if ($user && PasswordHandler::verify($password, $user['contra_empleado'])) {
        session_regenerate_id(true);
        $_SESSION['documento_empleado'] = $user['documento_empleado'];
        $_SESSION['nombre'] = $user['nombres_empleado'];
        $_SESSION['apellidos'] = $user['apellidos_empleado'];

        header('Location: ' . base_url('dashboard.php'));
        exit;
      } else {
        $mensaje = 'Credenciales inválidas.';
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
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Gestión de Inventarios</title>
    <link rel="stylesheet" href="assets/css/style.css"/>
  </head>
  <body class="login-page">
    <div class="login-container">
      <h1>Iniciar Sesión</h1>

    <?php if (!empty($mensaje)): ?>
      <div style="background-color: #f8d7da; color: #721c24; padding: 12px 16px; margin-bottom: 20px; border-radius: 8px; border: 1px solid #f5c6cb; font-size: 14px; text-align: center;">
        <?= htmlspecialchars($mensaje) ?></div>
    <?php endif; ?>

      <form action="" method="POST">
        <label for="email">Usuario</label>
        <input type="text" id="email" name="correo_empleado" placeholder="Ingresa tu correo" required/>
        <label for="password">Contraseña</label>
        <input type="password" id="password" name="contra_empleado" placeholder="••••••••" required/>
        <button type="submit" class="btn-primary">Entrar</button>
      </form>
      <a class="register" href="register.php">Registrarse</a>
    </div>
  </body>
</html>