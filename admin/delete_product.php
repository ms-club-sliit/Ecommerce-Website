<?php
require_once 'auth_check.php';
require_once '../config/database.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $productId = intval($_POST['id']);
    
    $conn = getDBConnection();
    
    // Get product image path before deleting
    $stmt = $conn->prepare("SELECT image FROM products WHERE id = ?");
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        
        // Delete product from database
        $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
        $stmt->bind_param("i", $productId);
        
        if ($stmt->execute()) {
            // Delete image file if exists
            if ($product['image'] && file_exists('../' . $product['image'])) {
                unlink('../' . $product['image']);
            }
            
            $response['success'] = true;
            $response['message'] = 'Product deleted successfully';
        } else {
            $response['message'] = 'Error deleting product: ' . $conn->error;
        }
    } else {
        $response['message'] = 'Product not found';
    }
    
    $stmt->close();
    $conn->close();
} else {
    $response['message'] = 'Invalid request';
}

echo json_encode($response);
?>
