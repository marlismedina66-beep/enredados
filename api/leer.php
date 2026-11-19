<?php
/**
 * leer.php
 * Obtiene todos los personajes registrados en la base de datos
 * Método: GET
 */

require_once __DIR__ . '/connect.php';

try {
    $stmt = $pdo->query("SELECT * FROM personajes ORDER BY fecha_registro DESC");
    $personajes = $stmt->fetchAll();

    echo json_encode([
        'success' => true,
        'mensaje' => 'Personajes obtenidos correctamente',
        'personajes' => $personajes
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al obtener personajes: ' . $e->getMessage()]);
}
