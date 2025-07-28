<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    echo "Aviso no especificado.";
    exit;
}

require_once '../models/Conexion.php';
require_once '../models/Aviso.php';

$conn = Conexion::conectar();
$id_aviso = $_GET['id'];

$stmt = $conn->prepare("
    SELECT a.*, u.latitud, u.longitud, u.direccion,
           p.fecha_perdida,
           ad.requisitos, ad.donaciones
    FROM AVISO a
    JOIN UBICACION u ON a.id_ubicacion = u.id_ubicacion
    LEFT JOIN AVISO_PERDIDA p ON a.id_aviso = p.id_aviso
    LEFT JOIN AVISO_ADOPCION ad ON a.id_aviso = ad.id_aviso
    WHERE a.id_aviso = ?
");
$stmt->execute([$id_aviso]);
$aviso = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$aviso) {
    echo "Aviso no encontrado.";
    exit;
}

$ruta_img = Aviso::obtenerImagenPorAviso($id_aviso);
$ruta_relativa = $ruta_img ? '../public/' . htmlspecialchars($ruta_img) : 'https://via.placeholder.com/300x200?text=Sin+imagen';
$tipo = Aviso::obtenerTipoAviso($id_aviso);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Detalle del Aviso</title>
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <link rel="stylesheet" href="../public/css/detalle_aviso.css">
</head>
<body>

<div class="container">
  <!-- Información -->
  <div class="card info-card">
    <h2><?= htmlspecialchars($aviso['nombre_mascota']) ?></h2>
    <img src="<?= $ruta_relativa ?>" alt="Foto de la mascota" />

    <div class="info"><strong>Tipo:</strong> <?= ucfirst($tipo) ?></div>
    <div class="info"><strong>Descripción:</strong><br><?= nl2br(htmlspecialchars($aviso['descripcion'])) ?></div>

    <?php if (!empty($aviso['estado_salud'])): ?>
      <div class="info"><strong>Estado de salud:</strong><br><?= nl2br(htmlspecialchars($aviso['estado_salud'])) ?></div>
    <?php endif; ?>

    <?php if ($tipo === 'adopcion'): ?>
      <?php if (!empty($aviso['requisitos'])): ?>
        <div class="info"><strong>Requisitos para adopción:</strong><br><?= nl2br(htmlspecialchars($aviso['requisitos'])) ?></div>
      <?php endif; ?>
      <div class="info"><strong>Acepta donaciones:</strong> <?= $aviso['donaciones'] ? 'Sí' : 'No' ?></div>
    <?php elseif ($tipo === 'perdida'): ?>
      <?php if (!empty($aviso['fecha_perdida'])): ?>
        <div class="info"><strong>Fecha de pérdida:</strong> <?= htmlspecialchars($aviso['fecha_perdida']) ?></div>
      <?php endif; ?>
    <?php endif; ?>

    <div class="info"><strong>Ubicación:</strong> <?= htmlspecialchars($aviso['direccion'] ?? 'No especificada') ?></div>
    <div class="info"><strong>Urgente:</strong> <?= $aviso['urgente'] ? '<span class="urgente">Sí</span>' : 'No' ?></div>
  </div>

  <!-- Mapa -->
  <div class="card map-card">
    <div id="map-title">📍 Ubicación del aviso</div>
    <div id="map"></div>
  </div>
</div>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
  const lat = <?= floatval($aviso['latitud']) ?>;
  const lng = <?= floatval($aviso['longitud']) ?>;
  const tipo = "<?= $tipo ?>";

  const map = L.map('map').setView([lat, lng], 15);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
  }).addTo(map);

  const color = tipo === 'perdida' ? 'red' : (tipo === 'adopcion' ? 'green' : 'blue');

  const marker = L.circleMarker([lat, lng], {
    radius: 10,
    fillColor: color,
    color: '#fff',
    weight: 2,
    opacity: 1,
    fillOpacity: 0.9
  }).addTo(map);
</script>

</body>
</html>
