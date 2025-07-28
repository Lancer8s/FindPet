<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Registro</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f7f9fc;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
      padding: 20px;
    }
    h1 {
      font-family: Arial, sans-serif;
      margin-bottom: 20px;
      color: #333;
    }
    form {
      background-color: white;
      padding: 30px 40px;
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
      display: flex;
      flex-direction: column;
      gap: 18px;
      width: 340px;
    }
    input[type="text"],
    input[type="email"],
    input[type="password"] {
      padding: 12px 15px;
      font-size: 16px;
      border: 1.5px solid #ccc;
      border-radius: 8px;
      transition: border-color 0.3s ease;
    }
    input:focus {
      border-color: #007bff;
      outline: none;
    }
    button[type="submit"] {
      background-color: #28a745;
      color: white;
      font-weight: 700;
      font-size: 16px;
      padding: 12px 0;
      border: none;
      border-radius: 10px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }
    button[type="submit"]:hover {
      background-color: #218838;
    }
    a.volver {
      margin-top: 15px;
      color: #007bff;
      text-decoration: none;
      font-weight: bold;
      font-size: 14px;
    }
    a.volver:hover {
      text-decoration: underline;
    }
    ::placeholder {
      color: #999;
    }
  </style>
</head>
<body>
  <h1>Crear una cuenta</h1>
  <form action="../controllers/UsuarioController.php" method="POST">
    <input type="text" name="nombre" required placeholder="Nombre completo" />
    <input type="email" name="correo" required placeholder="Correo electrónico" />
    <input type="text" name="telefono" required placeholder="Teléfono" />
    <input type="password" name="contrasenia" required placeholder="Contraseña" />
    <button type="submit" name="registrar">Registrarse</button>
  </form>
  <a class="volver" href="login.php">¿Ya tienes una cuenta? Inicia sesión</a>
</body>
</html>
