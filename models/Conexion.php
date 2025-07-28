<?php
class Conexion {
    public static function conectar() {
        try {
            $server = "localhost";
            $database = "FindPet";
            $username = "pet";
            $password = "publica";
            $conn = new PDO("sqlsrv:Server=$server;Database=$database", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }
}