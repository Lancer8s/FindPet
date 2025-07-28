<?php
session_start();
require_once '../models/Aviso.php';
require_once '../models/Ubicacion.php';

// 1. Eliminación
if (isset($_POST['eliminar'])) {
    if (!isset($_POST['id_aviso'])) {
        die("⚠️ Falta el ID del aviso a eliminar.");
    }
    Aviso::eliminar($_POST['id_aviso']);
    header("Location: ../views/mis_avisos.php");
    exit();
}

// 2. Actualización
if (isset($_POST['actualizar_aviso'])) {
    $id_aviso = $_POST['id_aviso'];
    $id_usuario = $_SESSION['id_usuario'];
    $nombre_mascota = $_POST['nombre_mascota'];
    $descripcion = $_POST['descripcion'];
    $estado_salud = $_POST['estado_salud'] ?? '';
    $urgente = isset($_POST['urgente']) ? 1 : 0;
    $direccion = $_POST['direccion'];
    $latitud = $_POST['latitud'];
    $longitud = $_POST['longitud'];
    $id_ubicacion = $_POST['id_ubicacion'];
    $tipo = $_POST['tipo_aviso'] ?? '';

    // Actualizar ubicación con la clase Ubicacion
    if ($id_ubicacion) {
        Ubicacion::actualizar($id_ubicacion, $direccion, $latitud, $longitud);
    } else {
        // Si por algún motivo no hay id_ubicacion, crea una nueva
        $id_ubicacion = Ubicacion::crear($direccion, $latitud, $longitud);
    }

    // Actualizar aviso
    Aviso::actualizar($id_aviso, $nombre_mascota, $descripcion, date('Y-m-d'), $urgente, $estado_salud, $id_usuario, $id_ubicacion);

    // Actualizar tablas hijas según tipo
    $conn = Conexion::conectar();

    if ($tipo === 'perdida') {
        $stmt = $conn->prepare("UPDATE AVISO_PERDIDA SET fecha_perdida = ? WHERE id_aviso = ?");
        $stmt->execute([$_POST['fecha_perdida'] ?? null, $id_aviso]);
    } elseif ($tipo === 'adopcion') {
        $stmt = $conn->prepare("UPDATE AVISO_ADOPCION SET requisitos = ?, donaciones = ? WHERE id_aviso = ?");
        $stmt->execute([$_POST['requisitos'] ?? '', isset($_POST['donaciones']) ? 1 : 0, $id_aviso]);
    }

    // Procesar imagen (si se subió una nueva)
    if (!empty($_FILES['imagen']['name'])) {
        $nombre_img = uniqid('img_') . '_' . basename($_FILES['imagen']['name']);
        $ruta_fisica = "../public/images/";
        $destino = $ruta_fisica . $nombre_img;

        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $destino)) {
            $ruta_web = "images/" . $nombre_img;

            // Verificar si ya existe imagen para el aviso
            $stmtCheck = $conn->prepare("SELECT COUNT(*) FROM IMAGEN WHERE id_aviso = ?");
            $stmtCheck->execute([$id_aviso]);
            if ($stmtCheck->fetchColumn() > 0) {
                // Actualiza registro existente
                $stmt = $conn->prepare("UPDATE IMAGEN SET ruta = ?, nombre_imagen = ?, tipo = ? WHERE id_aviso = ?");
                $stmt->execute([$ruta_web, $nombre_img, $_FILES['imagen']['type'], $id_aviso]);
            } else {
                // Inserta nuevo registro si no existe
                $stmt = $conn->prepare("INSERT INTO IMAGEN (ruta, nombre_imagen, tipo, id_aviso) VALUES (?, ?, ?, ?)");
                $stmt->execute([$ruta_web, $nombre_img, $_FILES['imagen']['type'], $id_aviso]);
            }
        }
    }

    header("Location: ../views/mis_avisos.php");
    exit();
}

// 3. Creación (solo si no es eliminar ni actualizar)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre_mascota'];
    $descripcion = $_POST['descripcion'];
    $estado_salud = $_POST['estado_salud'] ?? null;
    $urgente = isset($_POST['urgente']) ? 1 : 0;
    $tipo_aviso = $_POST['tipo_aviso'];
    $id_usuario = $_SESSION['id_usuario'] ?? ($_SESSION['usuario']['id_usuario'] ?? null);

    if (!$id_usuario) {
        die("⚠️ No se pudo obtener el ID del usuario.");
    }

    // Crear ubicación
    $direccion = $_POST['direccion'];
    $lat = $_POST['latitud'];
    $lng = $_POST['longitud'];
    $id_ubicacion = Ubicacion::crear($direccion, $lat, $lng);

    // Datos según tipo
    $fecha_perdida = $_POST['fecha_perdida'] ?? null;
    $requisitos = $_POST['requisitos'] ?? null;
    $donaciones = isset($_POST['donaciones']) ? 1 : 0;

    $id_aviso = Aviso::crear(
        $nombre,
        $descripcion,
        $urgente,
        $estado_salud,
        $id_usuario,
        $id_ubicacion,
        $tipo_aviso,
        $fecha_perdida,
        $requisitos,
        $donaciones
    );

    // Imagen
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $nombre_img = uniqid('img_') . '_' . basename($_FILES["imagen"]["name"]);
        $tipo_img = $_FILES["imagen"]["type"];
        $ruta_fisica = "../public/images/";
        $destino = $ruta_fisica . $nombre_img;

        if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $destino)) {
            $ruta_web = "images/" . $nombre_img;
            Aviso::guardarImagen($ruta_web, $nombre_img, $tipo_img, $id_aviso);
        }
    }

    header('Location: ../views/home.php');
    exit();
}
