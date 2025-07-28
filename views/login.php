<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Iniciar Sesión</title>
  <link rel="stylesheet" href="../public/css/login.css">
</head>
<body>
  <h1>Bienvenido a Find Pet</h1>

  <form action="../controllers/UsuarioController.php" method="POST">
    <input type="email" name="correo" required placeholder="Correo" />
    <input type="password" name="contrasenia" required placeholder="Contraseña" />
    <button type="submit" name="login">Iniciar sesión</button>
    <button type="button" class="register-button" onclick="location.href='register.php'">Registrarse</button>
  </form>
</body>
</html>
