<?php
/**
 * login.php
 * Inicia sesión de un usuario.
 * Método: POST
 * Cuerpo (JSON): { "email": "...", "password": "..." }
 * Respuesta: { "success": true, "usuario": {...} } | { "success": false, "error": "..." }
 */

header('Content-Type: application/json; charset=utf8mb4');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/connect.php';

// Recibir datos
$data = json_decode(file_get_contents('php://input'), true);

// Asegurar que existe la tabla de usuarios (si no existe)
$table_check = $pdo->query("SHOW TABLES LIKE 'usuarios'");
$table_exists = $table_check->fetch();

if (!$table_exists) {
    $pdo->exec("
        CREATE TABLE usuarios (
            id          INT AUTO_INCREMENT PRIMARY KEY,
            email       VARCHAR(100) NOT NULL UNIQUE,
            password    VARCHAR(255) NOT NULL,
            nombre      VARCHAR(100) NOT NULL,
            creado_en   TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci
    ");
}

$errors = [];
if (empty($data['email'])) {
    $errors[] = 'El correo es obligatorio';
}
if (empty($data['password'])) {
    $errors[] = 'La contraseña es obligatoria';
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Faltan datos',
        'detalles' => $errors
    ]);
    exit();
}

$email = trim($data['email']);
$password = $data['password'];

try {
    // Buscar usuario por correo
    $stmt = $pdo->prepare('SELECT id, email, nombre, password FROM usuarios WHERE email = :email');
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();

    if (!$user) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Correo o contraseña incorrectos']);
        exit();
    }

    // Verificar contraseña
    if (!password_verify($password, $user['password'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Correo o contraseña incorrectos']);
        exit();
    }

    // Responder con los datos del usuario (sin la contraseña)
    unset($user['password']);

    echo json_encode([
        'success' => true,
        'mensaje' => 'Inicio de sesión correcto',
        'usuario' => $user
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error al iniciar sesión: ' . $e->getMessage()
    ]);
}
