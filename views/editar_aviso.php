<?php
session_start();
require_once '../models/Aviso.php';

if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    echo "Aviso no especificado.";
    exit;
}

$id_aviso = $_GET['id'];
$aviso = Aviso::obtenerDetalleParaEditar($id_aviso);
$tipo = Aviso::obtenerTipoAviso($id_aviso);
$ruta_img = Aviso::obtenerImagenPorAviso($id_aviso);
$ruta_relativa = $ruta_img ? '../public/' . htmlspecialchars($ruta_img) : '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Editar Aviso</title>
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="../public/css/editar_aviso.css" />
</head>
<body>

<form action="../controllers/AvisoController.php" method="POST" enctype="multipart/form-data">
  <h3>Editar aviso</h3>

  <input type="hidden" name="id_aviso" value="<?= $aviso['id_aviso'] ?>">
  <input type="hidden" name="id_ubicacion" value="<?= $aviso['id_ubicacion'] ?>">

  <input type="text" name="nombre_mascota" required value="<?= htmlspecialchars($aviso['nombre_mascota']) ?>" placeholder="Nombre de la mascota" />
  <textarea name="descripcion" required rows="3" placeholder="Descripción del aviso"><?= htmlspecialchars($aviso['descripcion']) ?></textarea>

  <label><input type="checkbox" name="urgente" <?= $aviso['urgente'] ? 'checked' : '' ?>> Marcar como urgente</label>

  <textarea name="estado_salud" placeholder="Estado de salud (opcional)" rows="2"><?= htmlspecialchars($aviso['estado_salud'] ?? '') ?></textarea>

<h3>Tipo de aviso</h3>
<p style="margin-top: -10px; font-size: 16px; font-weight: 600; color: #333;">
  <?= $tipo === 'perdida' ? 'Pérdida' : 'Adopción' ?>
</p>
<input type="hidden" name="tipo_aviso" value="<?= $tipo ?>">

  <?php if ($tipo === 'perdida'): ?>
  <div id="campos_perdida" style="display: block;">
    <label for="fecha_perdida">Fecha de pérdida:</label>
    <input type="date" name="fecha_perdida" value="<?= $aviso['fecha_perdida'] ?? '' ?>">
  </div>
  <?php elseif ($tipo === 'adopcion'): ?>
  <div id="campos_adopcion" style="display: block;">
    <textarea name="requisitos" rows="2" placeholder="Requisitos para adoptar"><?= htmlspecialchars($aviso['requisitos'] ?? '') ?></textarea>
    <label><input type="checkbox" name="donaciones" <?= ($aviso['donaciones'] ?? false) ? 'checked' : '' ?>> Acepta donaciones</label>
  </div>
  <?php endif; ?>

  <h3>Imagen</h3>
  <?php if ($ruta_relativa): ?>
    <img src="<?= $ruta_relativa ?>" alt="Imagen actual" class="imagen-actual">
  <?php endif; ?>
  <input type="file" name="imagen" accept="image/*">

  <h3>Ubicación</h3>
  <input type="text" name="direccion" required value="<?= htmlspecialchars($aviso['direccion']) ?>" placeholder="Dirección" />
  <input type="hidden" name="latitud" id="latitud" value="<?= $aviso['latitud'] ?>">
  <input type="hidden" name="longitud" id="longitud" value="<?= $aviso['longitud'] ?>">
  <button type="submit" name="actualizar_aviso">Actualizar aviso</button>
</form>

</body>
</html>
