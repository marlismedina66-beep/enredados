<?php
/**
 * eliminar.php
 * Elimina un personaje de la base de datos
 * Método: DELETE
 * Datos recibidos: id
 */

require_once __DIR__ . '/connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido. Usa DELETE.']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id']) || !is_numeric($data['id']) || $data['id'] <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'ID inválido']);
    exit();
}

$id = (int)$data['id'];

try {
    $stmt = $pdo->prepare("DELETE FROM personajes WHERE id = ?");
    $stmt->execute([$id]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'mensaje' => 'Personaje eliminado correctamente']);
    } else {
        echo json_encode(['error' => 'No se encontró el personaje con ese ID']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al eliminar: ' . $e->getMessage()]);
}
