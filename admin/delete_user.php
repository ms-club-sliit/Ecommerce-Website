<?php
require_once 'auth_check.php';
require_once '../config/database.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $userId = intval($_POST['id']);
    
    $conn = getDBConnection();
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    
    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'User deleted successfully';
    } else {
        $response['message'] = 'Error deleting user: ' . $conn->error;
    }
    
    $stmt->close();
    $conn->close();
} else {
    $response['message'] = 'Invalid request';
}

echo json_encode($response);
?>
