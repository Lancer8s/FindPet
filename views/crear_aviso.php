<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Crear Aviso</title>
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <link rel="stylesheet" href="../public/css/crear_aviso.css">
</head>
<body>

  <form action="../controllers/AvisoController.php" method="POST" enctype="multipart/form-data">
    <!-- Información general -->
    <h3>Información de la mascota</h3>
    <input type="text" name="nombre_mascota" placeholder="Nombre de la mascota" required />
    <textarea name="descripcion" placeholder="Descripción" rows="3" required></textarea>

    <label>
      <input type="checkbox" name="urgente" />
      Marcar como urgente
    </label>

    <textarea name="estado_salud" placeholder="Estado de salud (opcional)" rows="2"></textarea>

    <!-- Tipo de aviso -->
    <h3>Tipo de aviso</h3>
    <select name="tipo_aviso" id="tipo_aviso" required onchange="mostrarCampos()">
      <option value="">Selecciona un tipo</option>
      <option value="perdida">Pérdida</option>
      <option value="adopcion">Adopción</option>
    </select>

    <!-- Datos adicionales según el tipo -->
    <div id="campos_perdida">
      <label for="fecha_perdida">Fecha de pérdida:</label>
      <input type="date" name="fecha_perdida" id="fecha_perdida" />
    </div>

    <div id="campos_adopcion">
      <textarea name="requisitos" placeholder="Requisitos para adopción" rows="2"></textarea>
      <label>
        <input type="checkbox" name="donaciones" />
        Acepta donaciones
      </label>
    </div>

    <!-- Imagen -->
    <h3>Imagen</h3>
    <input type="file" name="imagen" accept="image/*" required onchange="mostrarPreview(this)">
    <img id="preview" style="display: none;" alt="Vista previa">

    <!-- Ubicación -->
    <h3>Ubicación</h3>
    <input type="text" name="direccion" placeholder="Dirección exacta" required />
    <input type="hidden" name="latitud" id="latitud" />
    <input type="hidden" name="longitud" id="longitud" />
    <div id="map"></div>

    <!-- Botón de envío -->
    <button type="submit" name="crear_aviso">Crear aviso</button>
  </form>

  <script>
    function mostrarCampos() {
      const tipo = document.getElementById("tipo_aviso").value;
      document.getElementById("campos_perdida").style.display = tipo === "perdida" ? "block" : "none";
      document.getElementById("campos_adopcion").style.display = tipo === "adopcion" ? "block" : "none";
    }
  </script>

  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <script>
    let map;
    let marker;

    function initMap() {
      const cochabamba = [-17.3895, -66.1568];

      map = L.map('map').setView(cochabamba, 13);

      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
      }).addTo(map);

      // Crear el marcador inicial
      marker = L.marker(cochabamba, { draggable: true }).addTo(map);

      // Actualizar inputs cuando se arrastra el marcador
      marker.on('dragend', function () {
        const pos = marker.getLatLng();
        document.getElementById("latitud").value = pos.lat;
        document.getElementById("longitud").value = pos.lng;
      });

      // Actualizar inputs si el usuario hace clic en el mapa
      map.on('click', function (e) {
        const pos = e.latlng;
        marker.setLatLng(pos);
        document.getElementById("latitud").value = pos.lat;
        document.getElementById("longitud").value = pos.lng;
      });

      // Inicializar valores
      document.getElementById("latitud").value = cochabamba[0];
      document.getElementById("longitud").value = cochabamba[1];
    }

    window.onload = initMap;
    function mostrarPreview(input) {
      const file = input.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
          const img = document.getElementById('preview');
          img.src = e.target.result;
          img.style.display = 'block';
        };
        reader.readAsDataURL(file);
      }
    }
  </script>
</body>
</html>
