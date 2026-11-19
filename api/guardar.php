<?php
/**
 * guardar.php
 * Crea o actualiza un personaje en la base de datos
 * Método: POST
 * Datos recibidos: nombre, rol, aldea, poder, imagen, descripcion, ropa_cabello, ropa_camiseta, ropa_pantalon, ropa_zapatos
 */

require_once __DIR__ . '/connect.php';

// Verificar método
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido. Usa POST.']);
    exit();
}

// Recibir datos
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['error' => 'No se recibieron datos']);
    exit();
}

$nombre    = trim($data['nombre'] ?? '');
$rol       = trim($data['rol'] ?? '');
$aldea     = trim($data['aldea'] ?? '');
$poder     = isset($data['poder']) ? (int)$data['poder'] : null;
$imagen    = trim($data['imagen'] ?? '');
$descripcion = trim($data['descripcion'] ?? '');
$ropa_cabello       = trim($data['ropa_cabello'] ?? '');
$ropa_camiseta      = trim($data['ropa_camiseta'] ?? '');
$ropa_pantalon      = trim($data['ropa_pantalon'] ?? '');
$ropa_zapatos       = trim($data['ropa_zapatos'] ?? '');

// Validar campos obligatorios
$errores = [];
if ($nombre === '') $errores[] = 'El nombre es obligatorio';
if ($rol === '') $errores[] = 'El rol es obligatorio';
if ($imagen === '') $errores[] = 'La imagen es obligatoria';

if (!empty($errores)) {
    http_response_code(400);
    echo json_encode(['error' => 'Errores de validación', 'detalles' => $errores]);
    exit();
}

// Verificar si es actualización o creación
$id = isset($data['id']) ? (int)$data['id'] : 0;
if ($id > 0) {
    // Actualizar
    $stmt = $pdo->prepare("
        UPDATE personajes
        SET nombre = ?, rol = ?, aldea = ?, poder = ?, imagen = ?,
            descripcion = ?, ropa_cabello = ?, ropa_camiseta = ?, ropa_pantalon = ?, ropa_zapatos = ?
        WHERE id = ?
    ");
    $stmt->execute([$nombre, $rol, $aldea, $poder, $imagen, $descripcion, $ropa_cabello, $ropa_camiseta, $ropa_pantalon, $ropa_zapatos, $id]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'mensaje' => 'Personaje actualizado correctamente']);
    } else {
        // Si no se actualizó, crear
        $stmt = $pdo->prepare("
            INSERT INTO personajes (nombre, rol, aldea, poder, imagen, descripcion, ropa_cabello, ropa_camiseta, ropa_pantalon, ropa_zapatos)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$nombre, $rol, $aldea, $poder, $imagen, $descripcion, $ropa_cabello, $ropa_camiseta, $ropa_pantalon, $ropa_zapatos]);
        echo json_encode(['success' => true, 'mensaje' => 'Personaje registrado correctamente']);
    }
} else {
    // Crear nuevo
    $stmt = $pdo->prepare("
        INSERT INTO personajes (nombre, rol, aldea, poder, imagen, descripcion, ropa_cabello, ropa_camiseta, ropa_pantalon, ropa_zapatos)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$nombre, $rol, $aldea, $poder, $imagen, $descripcion, $ropa_cabello, $ropa_camiseta, $ropa_pantalon, $ropa_zapatos]);

    echo json_encode(['success' => true, 'mensaje' => 'Personaje registrado correctamente']);
}
