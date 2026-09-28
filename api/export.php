<?php
/**
 * SMART QR - Customer CSV Export API
 * Developed by BCA Students | Siddharth Shikshan Avam Prashikshan Sansthan
 * Mentored by Abhishek Chaudhary | ALAG INNOVATIVE SOLUTIONS
 *
 * GET /api/export.php
 * Strictly scoped to authenticated admin's restaurant_id
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

// Verify authentication
if (empty($_SESSION['admin_logged_in']) || empty($_SESSION['admin_restaurant_id'])) {
    http_response_code(401);
    die("Unauthorized: Please log in to export records.");
}

$restaurantId = (int)$_SESSION['admin_restaurant_id'];
$restaurant = get_restaurant_by_id($restaurantId);
$restaurantSlug = $restaurant ? $restaurant['slug'] : 'outlet';

// Filters
$search    = sanitize($_GET['search'] ?? '');
$type      = sanitize($_GET['type'] ?? '');
$consent   = sanitize($_GET['consent'] ?? '');
$dateRange = sanitize($_GET['date_range'] ?? 'all');
$startDate = sanitize($_GET['start_date'] ?? '');
$endDate   = sanitize($_GET['end_date'] ?? '');

// Build query
$where = ["restaurant_id = ?"];
$params = [$restaurantId];

if (!empty($search)) {
    $where[] = "(name LIKE ? OR mobile LIKE ?)";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
}

if (!empty($type) && in_array(strtolower($type), ['local', 'outsider'])) {
    $where[] = "customer_type = ?";
    $params[] = ucfirst(strtolower($type));
}

if ($consent !== '' && in_array($consent, ['0', '1'], true)) {
    $where[] = "marketing_consent = ?";
    $params[] = (int)$consent;
}

// Date Range Filtering
switch ($dateRange) {
    case 'today':
        $where[] = "created_at BETWEEN ? AND ?";
        $params[] = date('Y-m-d 00:00:00');
        $params[] = date('Y-m-d 23:59:59');
        break;
    case '7days':
        $where[] = "created_at >= ?";
        $params[] = date('Y-m-d 00:00:00', strtotime('-7 days'));
        break;
    case '30days':
        $where[] = "created_at >= ?";
        $params[] = date('Y-m-d 00:00:00', strtotime('-30 days'));
        break;
    case 'custom':
        if (!empty($startDate)) {
            $where[] = "created_at >= ?";
            $params[] = date('Y-m-d 00:00:00', strtotime($startDate));
        }
        if (!empty($endDate)) {
            $where[] = "created_at <= ?";
            $params[] = date('Y-m-d 23:59:59', strtotime($endDate));
        }
        break;
    default:
        // 'all' or empty - no additional date filter
        break;
}

$whereClause = implode(" AND ", $where);
$sql = "SELECT name, mobile, customer_type, bill_amount, marketing_consent, created_at 
        FROM customers 
        WHERE {$whereClause} 
        ORDER BY created_at DESC";

try {
    $db = get_db();
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll();

    // Stream CSV to client
    $filename = sprintf('smartqr_%s_%s.csv', preg_replace('/[^a-zA-Z0-9_-]/', '', $restaurantSlug), date('Y-m-d'));

    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');

    $output = fopen('php://output', 'w');

    // Add UTF-8 BOM so MS Excel renders Rupee symbols and Unicode correctly
    fputs($output, "\xEF\xBB\xBF");

    // Header row
    fputcsv($output, [
        'Customer Name',
        'Mobile Number',
        'Customer Type',
        'Bill Amount (INR)',
        'Marketing Consent',
        'Date & Time (IST)'
    ]);

    // Data rows
    foreach ($rows as $row) {
        fputcsv($output, [
            $row['name'],
            $row['mobile'],
            $row['customer_type'],
            number_format((float)$row['bill_amount'], 2, '.', ''),
            $row['marketing_consent'] == 1 ? 'Consent Given' : 'No Consent',
            date('d M Y, h:i A', strtotime($row['created_at']))
        ]);
    }

    fclose($output);
    exit;

} catch (PDOException $e) {
    error_log("CSV Export error: " . $e->getMessage());
    http_response_code(500);
    echo "Error generating CSV export.";
    exit;
}
