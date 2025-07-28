// /public/js/editar_aviso.js

function mostrarCampos() {
  let tipo = document.getElementById("tipo_aviso").value;
  document.getElementById("campos_perdida").style.display = (tipo === "perdida") ? "block" : "none";
  document.getElementById("campos_adopcion").style.display = (tipo === "adopcion") ? "block" : "none";
}

window.onload = function () {
  mostrarCampos();

  const latInput = document.getElementById("latitud").value;
  const lngInput = document.getElementById("longitud").value;
  const lat = parseFloat(latInput);
  const lng = parseFloat(lngInput);
  const mapDiv = document.getElementById("map");
  const msg = document.getElementById("map_msg");

  const coordsValidas = !isNaN(lat) && !isNaN(lng);

  if (!coordsValidas) {
    mapDiv.style.display = "none";
    msg.style.display = "block";
    return;
  }

  const map = L.map('map').setView([lat, lng], 14);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

  const marker = L.marker([lat, lng], { draggable: true }).addTo(map);

  marker.on('dragend', function () {
    const pos = marker.getLatLng();
    document.getElementById("latitud").value = pos.lat;
    document.getElementById("longitud").value = pos.lng;
  });

  map.on('click', function (e) {
    const pos = e.latlng;
    marker.setLatLng(pos);
    document.getElementById("latitud").value = pos.lat;
    document.getElementById("longitud").value = pos.lng;
  });

  // Corrige tamaño del mapa si el contenedor no estaba visible al principio
  setTimeout(() => {
    map.invalidateSize();
  }, 700);
};


