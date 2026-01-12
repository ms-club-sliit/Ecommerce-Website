<?php
require_once 'auth_check.php';
require_once '../config/database.php';

$conn = getDBConnection();
$users = $conn->query("SELECT * FROM users ORDER BY created_at DESC");

// Generate HTML for PDF
$html = '
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; }
        h1 { color: #2c3e50; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #3498db; color: white; padding: 10px; text-align: left; }
        td { padding: 8px; border-bottom: 1px solid #ddd; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        .header { text-align: center; margin-bottom: 30px; }
        .footer { margin-top: 30px; text-align: center; font-size: 12px; color: #7f8c8d; }
    </style>
</head>
<body>
    <div class="header">
        <h1>VedaLife - Users Report</h1>
        <p>Generated on: ' . date('F d, Y H:i:s') . '</p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Registration Date</th>
            </tr>
        </thead>
        <tbody>';

while ($user = $users->fetch_assoc()) {
    $html .= '<tr>
        <td>' . $user['id'] . '</td>
        <td>' . htmlspecialchars($user['name']) . '</td>
        <td>' . htmlspecialchars($user['email']) . '</td>
        <td>' . htmlspecialchars($user['phone']) . '</td>
        <td>' . date('M d, Y', strtotime($user['created_at'])) . '</td>
    </tr>';
}

$html .= '
        </tbody>
    </table>
    
    <div class="footer">
        <p>Total Users: ' . $users->num_rows . '</p>
        <p>&copy; ' . date('Y') . ' VedaLife Ayurveda. All rights reserved.</p>
    </div>
</body>
</html>';

$conn->close();

// Use DomPDF or wkhtmltopdf if available, otherwise export as HTML
// For simplicity, we'll use browser's print to PDF functionality
header('Content-Type: text/html; charset=utf-8');
header('Content-Disposition: inline; filename="users_report_' . date('Y-m-d') . '.html"');

echo $html;
echo '<script>window.print();</script>';
exit;
?>
