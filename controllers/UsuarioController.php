<?php
// Muestra todos los errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once '../models/Conexion.php';

// REGISTRO DE USUARIO
if (isset($_POST['registrar'])) {
    try {
        $conn = Conexion::conectar();

        // Validar campos
        if (!isset($_POST['nombre'], $_POST['correo'], $_POST['telefono'], $_POST['contrasenia'])) {
            throw new Exception("Faltan campos en el formulario.");
        }

        $nombre = $_POST['nombre'];
        $correo = $_POST['correo'];
        $telefono = $_POST['telefono'];
        $contrasenia = password_hash($_POST['contrasenia'], PASSWORD_BCRYPT);

        // Insertar nuevo usuario
        $stmt = $conn->prepare("INSERT INTO USUARIO (nombre, correo, telefono, contrasenia) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nombre, $correo, $telefono, $contrasenia]);

        // Obtener el usuario recién registrado
        $stmt = $conn->prepare("SELECT * FROM USUARIO WHERE correo = ?");
        $stmt->execute([$correo]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        // Guardar en sesión
        $_SESSION['id_usuario'] = $usuario['id_usuario'];
        $_SESSION['nombre'] = $usuario['nombre'];

        // Redirigir a home
        header("Location: ../views/home.php");
        exit();
    } catch (PDOException $e) {
        echo "⚠️ Error en el registro (PDO): " . $e->getMessage();
        exit();
    } catch (Exception $e) {
        echo "⚠️ Error general: " . $e->getMessage();
        exit();
    }
}

// INICIO DE SESIÓN
if (isset($_POST['login'])) {
    try {
        $conn = Conexion::conectar();

        if (!isset($_POST['correo'], $_POST['contrasenia'])) {
            throw new Exception("Faltan campos.");
        }

        $correo = $_POST['correo'];
        $contrasenia = $_POST['contrasenia'];

        $stmt = $conn->prepare("SELECT * FROM USUARIO WHERE correo = ?");
        $stmt->execute([$correo]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($contrasenia, $usuario['contrasenia'])) {
            $_SESSION['id_usuario'] = $usuario['id_usuario'];
            $_SESSION['nombre'] = $usuario['nombre'];
            header("Location: ../views/home.php");
        } else {
            header("Location: ../views/login.php?error=credenciales");
        }
        exit();
    } catch (PDOException $e) {
        echo "⚠️ Error al iniciar sesión: " . $e->getMessage();
        exit();
    }
}
