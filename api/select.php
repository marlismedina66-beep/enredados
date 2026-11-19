<?php
/**
 * select.php
 * Obtiene un personaje específico por ID
 * Método: GET
 * Parámetros: id
 */

require_once __DIR__ . '/connect.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'ID inválido']);
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT * FROM personajes WHERE id = ?");
    $stmt->execute([$id]);
    $personaje = $stmt->fetch();

    if ($personaje) {
        echo json_encode(['success' => true, 'personaje' => $personaje]);
    } else {
        echo json_encode(['error' => 'Personaje no encontrado']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al obtener personaje: ' . $e->getMessage()]);
}
