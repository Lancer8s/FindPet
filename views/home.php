<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

require_once '../models/Conexion.php';
require_once '../models/Aviso.php';

$conn = Conexion::conectar();
$stmt = $conn->query("EXEC sp_listar_avisos");
$avisos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>FindPet - Home</title>
    <link rel="stylesheet" href="../public/css/home.css">
</head>
<body>

<header>
    <div class="nav-links">
        <a href="mis_avisos.php" class="btn-misavisos">Mis Avisos</a>
        <a href="mapa_general.php" class="btn-mapa">Ver Mapa de Mascotas</a>
    </div>
    <form action="../logout.php" method="POST">
        <button type="submit" style="
    background-color: #dc3545; /* rojo agradable */
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.3s ease;
    font-family: Arial, sans-serif;
">
    Cerrar sesión
</button>
    </form>
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
        <p class="ubicacion">Ubicación: <?= htmlspecialchars($aviso['direccion'] ?? 'No especificada') ?></p>
        <a href="detalle_aviso.php?id=<?= $aviso['id_aviso'] ?>" class="btn-verdetalle">Ver Detalle</a>
        <button class="btn-whatsapp" title="Contactar por WhatsApp">WhatsApp</button>
    </article>
    <?php endforeach; ?>
</main>

</body>
</html>
