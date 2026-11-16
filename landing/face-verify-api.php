<?php
session_start();
require '../config/database.php';
require_once '../includes/rider-face.php';
header('Content-Type: application/json');
header('Cache-Control: no-store');

function face_reply(int $status, array $data): void
{
    http_response_code($status);
    echo json_encode($data);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    face_reply(405, ['error' => 'POST is required.']);
}
$riderId = rider_face_pending_id();
if ($riderId === null) {
    face_reply(401, ['error' => 'Your verification session expired. Please log in again.']);
}
if (!isset($_SESSION['rider_face_csrf']) || !is_string($_POST['csrf'] ?? null) ||
    !hash_equals($_SESSION['rider_face_csrf'], $_POST['csrf'])) {
    face_reply(403, ['error' => 'Invalid request. Reload this page.']);
}
$window = $_SESSION['rider_face_attempts'] ?? ['start' => time(), 'count' => 0];
if (time() - $window['start'] >= 60) {
    $window = ['start' => time(), 'count' => 0];
}
if ($window['count'] >= 5) {
    header('Retry-After: ' . max(1, 60 - (time() - $window['start'])));
    face_reply(429, ['error' => 'Too many scans. Wait one minute and try again.']);
}
$window['count']++;
$_SESSION['rider_face_attempts'] = $window;
$photo = $_FILES['photo'] ?? null;
if (!is_array($photo) || ($photo['error'] ?? -1) !== UPLOAD_ERR_OK ||
    !is_int($photo['size']) || $photo['size'] < 1 || $photo['size'] > 2 * 1024 * 1024 ||
    !is_string($photo['tmp_name']) || !is_uploaded_file($photo['tmp_name'])) {
    face_reply(422, ['error' => 'Capture a camera image smaller than 2 MB and try again.']);
}

$attendanceTransaction = false;
try {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    // Identity and registered photo come ONLY from the server database.
    $stmt = $conn->prepare("SELECT u.id, u.full_name, p.photo_bytes
        FROM users u LEFT JOIN rider_registered_faces p ON p.rider_id = u.id
        WHERE u.id = ? AND u.role = 'rider'");
    $stmt->bind_param('i', $riderId);
    $stmt->execute();
    $stmt->bind_result($id, $fullName, $registeredPhoto);
    $found = $stmt->fetch();
    $stmt->close();
    if (!$found) {
        unset($_SESSION['rider_face_pending'], $_SESSION['rider_face_csrf']);
        face_reply(401, ['error' => 'Rider account unavailable. Please log in again.']);
    }
    if (!$registeredPhoto) {
        face_reply(422, ['error' => 'No registered face. Ask your administrator to enroll your face.']);
    }
    $scan = rider_face_image($photo['tmp_name']);
    if (!rider_face_matches($registeredPhoto, $scan)) {
        face_reply(422, ['error' => 'Face did not match your registered face. Please try again.']);
    }

    // Choose the attendance day AFTER matching, in the configured business timezone.
    $now = rider_face_now();
    $day = $now->format('Y-m-d');
    $utc = $now->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d H:i:s');
    // The unique key resolves simultaneous requests from different sessions.
    // Existing time-in remains unchanged; other database errors still fail.
    $conn->begin_transaction();
    $attendanceTransaction = true;
    $stmt = $conn->prepare('INSERT INTO rider_attendance (rider_id, attendance_date, time_in_utc)
        VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE rider_id = rider_id');
    $stmt->bind_param('iss', $riderId, $day, $utc);
    $stmt->execute();
    $stmt->close();
    $conn->commit();
    $attendanceTransaction = false;

    // Promote to authenticated rider ONLY AFTER the attendance write succeeds.
    session_regenerate_id(true);
    $_SESSION['user_id'] = $riderId;
    $_SESSION['full_name'] = $fullName;
    $_SESSION['role'] = 'rider';
    $_SESSION['rider_face_day'] = $day;
    unset($_SESSION['rider_face_pending'], $_SESSION['rider_face_csrf']);
    face_reply(200, ['redirect' => '../employee/employee.php']);
} catch (RiderFaceInputException $e) {
    face_reply(422, ['error' => $e->getMessage()]);
} catch (RiderFaceServiceException $e) {
    face_reply(503, ['error' => $e->getMessage()]);
} catch (Throwable $e) {
    if ($attendanceTransaction) {
        try {
            $conn->rollback();
        } catch (Throwable $rollbackError) {
            error_log('Rider attendance rollback failed');
        }
    }
    error_log('Rider face verification: ' . $e->getMessage());
    face_reply(503, ['error' => 'Could not verify your scan or save time-in. Use one clear face and try again. If this continues, contact your administrator.']);
}
