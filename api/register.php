<?php
/**
 * register.php
 * Registra un nuevo usuario en la base de datos.
 * Método: POST
 * Cuerpo (JSON): { "email": "...", "password": "...", "nombre": "..." }
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
if (empty($data['nombre'])) {
    $errors[] = 'El nombre es obligatorio';
}

if (!empty($errors)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Errores de validación',
        'detalles' => $errors
    ]);
    exit();
}

$email = trim($data['email']);
$password = $data['password'];
$nombre = trim($data['nombre']);

// Validar correo
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'El correo no es válido']);
    exit();
}

// Validar longitud de la contraseña
if (strlen($password) < 6) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'La contraseña debe tener al menos 6 caracteres']);
    exit();
}

// Hashear la contraseña
$password_hash = password_hash($password, PASSWORD_DEFAULT);

try {
    // Verificar que el correo no esté registrado
    $check = $pdo->prepare('SELECT id FROM usuarios WHERE email = :email');
    $check->execute([':email' => $email]);
    if ($check->fetch()) {
        http_response_code(409);
        echo json_encode(['success' => false, 'error' => 'Ya existe un usuario con este correo']);
        exit();
    }

    // Insertar nuevo usuario
    $stmt = $pdo->prepare('INSERT INTO usuarios (email, password, nombre) VALUES (:email, :password, :nombre)');
    $stmt->execute([
        ':email' => $email,
        ':password' => $password_hash,
        ':nombre' => $nombre
    ]);

    echo json_encode([
        'success' => true,
        'mensaje' => 'Usuario registrado correctamente'
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error al registrar usuario: ' . $e->getMessage()
    ]);
}
