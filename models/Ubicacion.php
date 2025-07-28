<?php
require_once 'Conexion.php';

class Ubicacion {
    public static function crear($direccion, $lat, $lng) {
        $conn = Conexion::conectar();
        $stmt = $conn->prepare("INSERT INTO UBICACION (direccion, latitud, longitud) VALUES (?, ?, ?)");
        $stmt->execute([$direccion, $lat, $lng]);
        return $conn->lastInsertId(); // IDENTITY en SQL Server
    }

    public static function actualizar($id_ubicacion, $direccion, $latitud, $longitud) {
        $conn = Conexion::conectar();
        $stmt = $conn->prepare("UPDATE UBICACION SET direccion = ?, latitud = ?, longitud = ? WHERE id_ubicacion = ?");
        $stmt->execute([$direccion, $latitud, $longitud, $id_ubicacion]);
    }
}
