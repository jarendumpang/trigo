<?php
session_start();
require_once __DIR__ . '/../includes/rider-face.php';
header('Content-Type: application/json');
header('Cache-Control: no-store');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit(json_encode(['error' => 'POST required.']));
}
$isAdmin = ($_SESSION['role'] ?? '') === 'admin' && !empty($_SESSION['user_id']);
if (!$isAdmin && rider_face_pending_id() === null) {
    http_response_code(401);
    exit(json_encode(['error' => 'Please log in again.']));
}
$expected = $_SESSION[$isAdmin ? 'admin_face_csrf' : 'rider_face_csrf'] ?? '';
if ($expected === '' || !is_string($_POST['csrf'] ?? null) || !hash_equals($expected, $_POST['csrf'])) {
    http_response_code(403);
    exit(json_encode(['error' => 'Invalid request. Reload the page.']));
}
session_write_close();
// Short health requests let a free sleeping host wake without one long PHP request.
try {
    $status = rider_face_service('health', [], 3);
    echo json_encode(['ready' => ($status['ready'] ?? false) === true]);
} catch (Throwable $e) {
    echo json_encode(['ready' => false]);
}
