<?php
class Usuario {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function login($correo, $contrasenia) {
        $sql = "SELECT * FROM USUARIO WHERE correo = ? AND contrasenia = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$correo, $contrasenia]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function registrar($nombre, $correo, $telefono, $contrasenia) {
        $sql = "INSERT INTO USUARIO (nombre, correo, telefono, contrasenia) VALUES (?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$nombre, $correo, $telefono, $contrasenia]);
    }
}
