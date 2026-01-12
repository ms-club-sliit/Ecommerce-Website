<?php
require_once 'auth_check.php';
require_once '../config/database.php';

$conn = getDBConnection();
$products = $conn->query("SELECT * FROM products ORDER BY created_at DESC");

// Generate HTML for PDF
$html = '
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; }
        h1 { color: #2c3e50; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #27ae60; color: white; padding: 10px; text-align: left; }
        td { padding: 8px; border-bottom: 1px solid #ddd; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        .header { text-align: center; margin-bottom: 30px; }
        .footer { margin-top: 30px; text-align: center; font-size: 12px; color: #7f8c8d; }
        .low-stock { color: #e74c3c; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>VedaLife - Products Report</h1>
        <p>Generated on: ' . date('F d, Y H:i:s') . '</p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Product Name</th>
                <th>Category</th>
                <th>Price (₹)</th>
                <th>Stock</th>
                <th>Created Date</th>
            </tr>
        </thead>
        <tbody>';

$totalValue = 0;
while ($product = $products->fetch_assoc()) {
    $stockClass = $product['stock'] < 10 ? ' class="low-stock"' : '';
    $totalValue += $product['price'] * $product['stock'];
    
    $html .= '<tr>
        <td>' . $product['id'] . '</td>
        <td>' . htmlspecialchars($product['name']) . '</td>
        <td>' . htmlspecialchars($product['category']) . '</td>
        <td>₹' . number_format($product['price'], 2) . '</td>
        <td' . $stockClass . '>' . $product['stock'] . '</td>
        <td>' . date('M d, Y', strtotime($product['created_at'])) . '</td>
    </tr>';
}

$html .= '
        </tbody>
    </table>
    
    <div class="footer">
        <p><strong>Total Products:</strong> ' . $products->num_rows . '</p>
        <p><strong>Total Inventory Value:</strong> ₹' . number_format($totalValue, 2) . '</p>
        <p>&copy; ' . date('Y') . ' VedaLife Ayurveda. All rights reserved.</p>
    </div>
</body>
</html>';

$conn->close();

// Output HTML for browser print to PDF
header('Content-Type: text/html; charset=utf-8');
header('Content-Disposition: inline; filename="products_report_' . date('Y-m-d') . '.html"');

echo $html;
echo '<script>window.print();</script>';
exit;
?>
