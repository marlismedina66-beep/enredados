<?php
/**
 * loading.php
 * Cuenta cuántos personajes están cargados (para mostrar "Cargando...")
 * Método: GET
 */

require_once __DIR__ . '/connect.php';

try {
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM personajes");
    $total = $stmt->fetchColumn();

    echo json_encode(['success' => true, 'total' => $total]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al contar personajes: ' . $e->getMessage()]);
}
