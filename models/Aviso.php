<?php
require_once 'Conexion.php';

class Aviso {
    public static function crear($nombre, $descripcion, $urgente, $estado_salud, $id_usuario, $id_ubicacion, $tipo_aviso, $fecha_perdida = null, $requisitos = null, $donaciones = 0) {
        $conn = Conexion::conectar();
        $stmt = $conn->prepare("EXEC sp_crear_aviso ?, ?, DEFAULT, ?, ?, ?, ?, ?, ?, ?, ?");

        $stmt->execute([
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
        ]);

        // Manejar múltiples conjuntos de resultados
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($resultado === false && $stmt->nextRowset()) {
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        return $resultado['id_aviso_creado'] ?? null;
    }




    public static function listarPorUsuario($id_usuario) {
        $conn = Conexion::conectar();
        $stmt = $conn->prepare("EXEC sp_listar_avisos_por_usuario ?");
        $stmt->execute([$id_usuario]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function eliminar($id_aviso) {
        $conn = Conexion::conectar();

        // Eliminar de tablas hijas (por si existen registros relacionados)
        $stmt = $conn->prepare("DELETE FROM AVISO_PERDIDA WHERE id_aviso = ?");
        $stmt->execute([$id_aviso]);

        $stmt = $conn->prepare("DELETE FROM AVISO_ADOPCION WHERE id_aviso = ?");
        $stmt->execute([$id_aviso]);

        $stmt = $conn->prepare("DELETE FROM IMAGEN WHERE id_aviso = ?");
        $stmt->execute([$id_aviso]);

        // Finalmente eliminar el aviso
        $stmt = $conn->prepare("DELETE FROM AVISO WHERE id_aviso = ?");
        $stmt->execute([$id_aviso]);
    }


    public static function actualizar($id_aviso, $nombre, $descripcion, $fecha, $urgente, $estado_salud, $id_usuario, $id_ubicacion) {
        $conn = Conexion::conectar();
        $stmt = $conn->prepare("EXEC sp_actualizar_aviso ?, ?, ?, ?, ?, ?, ?, ?");
        $stmt->execute([$id_aviso, $nombre, $descripcion, $fecha, $urgente, $estado_salud, $id_usuario, $id_ubicacion]);
    }
    public static function guardarImagen($ruta, $nombre_archivo, $tipo, $id_aviso) {
        $conn = Conexion::conectar();
        $stmt = $conn->prepare("INSERT INTO IMAGEN (ruta, nombre_imagen, tipo, id_aviso) VALUES (?, ?, ?, ?)");
        $stmt->execute([$ruta, $nombre_archivo, $tipo, $id_aviso]);
    }
    public static function obtenerImagenPorAviso($id_aviso) {
        $conn = Conexion::conectar();
        $stmt = $conn->prepare("SELECT TOP 1 ruta FROM IMAGEN WHERE id_aviso = ?");
        $stmt->execute([$id_aviso]);
        return $stmt->fetchColumn();
    }
    public static function obtenerTipoAviso($id_aviso) {
        $conn = Conexion::conectar();

        // Verificar si es pérdida
        $stmt = $conn->prepare("SELECT COUNT(*) FROM AVISO_PERDIDA WHERE id_aviso = ?");
        $stmt->execute([$id_aviso]);
        if ($stmt->fetchColumn() > 0) {
            return 'perdida';
        }

        // Verificar si es adopción
        $stmt = $conn->prepare("SELECT COUNT(*) FROM AVISO_ADOPCION WHERE id_aviso = ?");
        $stmt->execute([$id_aviso]);
        if ($stmt->fetchColumn() > 0) {
            return 'adopcion';
        }

        return null;
    }
    public static function obtenerDetalleParaEditar($id_aviso) {
        $db = Conexion::conectar();

        $sql = "SELECT a.*, u.direccion, u.latitud, u.longitud
                FROM AVISO a
                JOIN UBICACION u ON a.id_ubicacion = u.id_ubicacion
                WHERE a.id_aviso = :id_aviso";

        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id_aviso', $id_aviso, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

}
