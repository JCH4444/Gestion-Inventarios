<?php
require __DIR__ . '/db.php';

$mensaje = '';
$ok = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Captura los campos del formulario
  $documento = trim($_POST['documento_empleado'] ?? '');
  $nombres   = trim($_POST['nombres_empleado'] ?? '');
  $apellidos = trim($_POST['apellidos_empleado'] ?? '');
  $correo    = trim($_POST['correo_empleado'] ?? '');
  $contra    = $_POST['contra_empleado'] ?? '';
  $telefono  = trim($_POST['telefono_empleado'] ?? '');
  
  // Si el teléfono está vacío, usar NULL
  $telefono = ($telefono === '') ? null : $telefono;
  
  // Validación de campos obligatorios
  if ($documento === '' || $nombres === '' || $apellidos === '' || $correo === '' || $contra === '') {
    $mensaje = 'Todos los campos son obligatorios excepto teléfono.';
  } else {
    try {
      // Verifica si el documento o correo ya existen
      $stmt = $pdo->prepare("SELECT 1 FROM empleado WHERE documento_empleado = ? OR correo_empleado = ?");
      $stmt->execute([$documento, $correo]);
      
      if ($stmt->fetch()) {
        $mensaje = 'El documento o correo ya existe en el sistema.';
      } else {
        // Encripta la contraseña
        $hash = password_hash($contra, PASSWORD_BCRYPT);
        
        // INSERT solo con los 6 campos que el usuario ingresa
        // NO incluir fecha_ingreso_empleado ni estado_empleado
        $ins = $pdo->prepare(
          "INSERT INTO empleado (documento_empleado, nombres_empleado, apellidos_empleado, correo_empleado, contra_empleado, telefono_empleado)
          VALUES (?, ?, ?, ?, ?, ?)"
        );
        
        // Ejecuta y verifica el resultado
        if ($ins->execute([$documento, $nombres, $apellidos, $correo, $hash, $telefono])) {
          $ok = 'Empleado registrado correctamente. Redirigiendo al login...';
          // Redirige después de 2 segundos
          header('Refresh: 2; URL=' . base_url('login.php'));
          exit; // Detiene la ejecución después de la redirección
        } else {
          // Si execute() retorna false
          $errorInfo = $ins->errorInfo();
          $mensaje = 'Error al insertar: ' . $errorInfo[2];
        }
      }
    } catch (PDOException $e) {
      // Captura errores específicos de PDO
      $mensaje = 'Error de base de datos: ' . $e->getMessage();
    } catch (Exception $e) {
      // Captura cualquier otro error
      $mensaje = 'Error general: ' . $e->getMessage();
    }
  }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Registro – Sistema de Inventarios</title>
  <link rel="stylesheet" href="assets/css/style.css"/>
</head>
<body class="login-page">
  <div class="login-container">    
    <h1>Crear Cuenta</h1>

  <?php if ($mensaje): ?>
    <div style="background-color: #f8d7da; color: #721c24; padding: 12px 16px; margin-bottom: 20px; border-radius: 8px; border: 1px solid #f5c6cb; font-size: 14px; text-align: center;">
      <?= htmlspecialchars($mensaje) ?></div>
  <?php endif; ?>

  <?php if ($ok): ?>
    <div style="background-color: #d4edda; color: #155724; padding: 12px 16px; margin-bottom: 20px; border-radius: 8px; border: 1px solid #c3e6cb; font-size: 14px; text-align: center;">
      <?= htmlspecialchars($ok) ?></div>
  <?php endif; ?>

    <form id="registerForm" method="POST" action="">
      <label for="documento">Documento</label>
      <input type="text" id="documento" name="documento_empleado" placeholder="Ej: 1000..." required />
      
      <label for="names">Nombres</label>
      <input type="text" id="names" name="nombres_empleado" placeholder="Ej: Juan Andres" required />

      <label for="lastnames">Nombres</label>
      <input type="text" id="lastnames" name="apellidos_empleado" placeholder="Sanchez Castaño" required />

      <label for="email">Correo Electrónico</label>
      <input type="email" id="email" name="correo_empleado" placeholder="Ej: correo@ejemplo.com" required />

      <label for="password">Contraseña</label>
      <input type="password" id="password" name="contra_empleado" placeholder="••••••••" required />

      <label for="celphone">Telefono</label>
      <input type="text" id="celphone" name="telefono_empleado" placeholder="Ej: 123456789"/>

      <div class="checkbox-group">
        <input type="checkbox" id="terms" name="terms" required/>
        <label for="terms">Acepto los <a href="#">términos y condiciones</a></label>
      </div>

      <button type="submit" class="btn-primary">Registrarse</button>
    </form>
    <p>
      ¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a>
    </p>
  </div>
</body>
</html>
