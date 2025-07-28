<?php
session_start();
require_once '../models/Aviso.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

$avisos = Aviso::listarPorUsuario($_SESSION['id_usuario']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>FindPet - Mis Avisos</title>
    <link rel="stylesheet" href="../public/css/mis_avisos.css">
</head>
<body>

<header>
    <h1>Mis Avisos</h1>
    <a href="crear_aviso.php" class="nuevo-aviso-link">+ Nuevo Aviso</a>
</header>

<main>
    <?php foreach ($avisos as $aviso):
        $tipo = Aviso::obtenerTipoAviso($aviso['id_aviso']);
        $borderClass = ($tipo === 'perdida') ? 'red-border' : 'green-border';
        $ruta_img = Aviso::obtenerImagenPorAviso($aviso['id_aviso']);
        $ruta_relativa = $ruta_img ? '../public/' . htmlspecialchars($ruta_img) : 'https://via.placeholder.com/300x180?text=Sin+imagen';
    ?>
    <article class="aviso-card <?= $borderClass ?>">
        <h3><?= htmlspecialchars($aviso['nombre_mascota']) ?></h3>
        <img src="<?= $ruta_relativa ?>" alt="Imagen de <?= htmlspecialchars($aviso['nombre_mascota']) ?>">
        <p><?= htmlspecialchars($aviso['descripcion']) ?></p>
        <p><strong>Ubicación:</strong> <?= htmlspecialchars($aviso['direccion'] ?? 'No especificada') ?></p>
        <a href="editar_aviso.php?id=<?= $aviso['id_aviso'] ?>" class="btn-editar">✏️ Editar</a>
        <form action="../controllers/AvisoController.php" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar este aviso?')">
            <input type="hidden" name="id_aviso" value="<?= $aviso['id_aviso'] ?>">
            <button type="submit" name="eliminar">🗑 Eliminar</button>
        </form>
    </article>
    <?php endforeach; ?>
</main>

</body>
</html>
