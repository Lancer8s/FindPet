<?php
session_start();
require_once '../models/Conexion.php';
$conn = Conexion::conectar();

$stmt = $conn->query("EXEC sp_listar_avisos");
$avisos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Mapa de Mascotas</title>
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
  <style>
    #map { height: 600px; width: 90%; margin: 20px auto; border: 2px solid #ccc; }
  </style>
</head>
<body>

<h2 style="text-align:center;">📍 Mapa de Mascotas 📍</h2>
<div id="map"></div>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
const map = L.map('map').setView([-17.3895, -66.1568], 13);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
  attribution: '&copy; OpenStreetMap contributors'
}).addTo(map);

const avisos = <?= json_encode($avisos, JSON_UNESCAPED_UNICODE) ?>;

avisos.forEach(aviso => {
  if (!aviso.latitud || !aviso.longitud) return;

  const color = aviso.tipo === 'perdida' ? 'red' : (aviso.tipo === 'adopcion' ? 'green' : 'blue');

  const marker = L.circleMarker([aviso.latitud, aviso.longitud], {
    radius: 8,
    fillColor: color,
    color: '#fff',
    weight: 1,
    opacity: 1,
    fillOpacity: 0.9
  }).addTo(map);

  marker.bindPopup(`<strong>${aviso.nombre_mascota}</strong><br>${aviso.descripcion}`);
});
</script>

</body>
</html>
