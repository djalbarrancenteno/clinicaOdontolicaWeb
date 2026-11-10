<?php
session_start();
require_once '../conexion.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $clave = trim($_POST['clave'] ?? '');

    if (empty($email) || empty($clave)) {
        echo json_encode(['status' => 'error', 'message' => 'Por favor, complete todos los campos.']);
        exit();
    }

    try {
        $stmt = $conexion->prepare("SELECT id, nombre, apellido, clave FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();

        if ($usuario && $usuario['clave'] === $clave) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'] . ' ' . $usuario['apellido'];

            echo json_encode(['status' => 'success', 'message' => 'Autenticación exitosa. Redirigiendo...']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Correo electrónico o contraseña incorrectos.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Método no permitido.']);
}
?>