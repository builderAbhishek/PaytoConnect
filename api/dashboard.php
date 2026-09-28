<?php
/**
 * SMART QR - Admin Dashboard Metrics API
 * Developed by BCA Students | Siddharth Shikshan Avam Prashikshan Sansthan
 * Mentored by Abhishek Chaudhary | ALAG INNOVATIVE SOLUTIONS
 *
 * GET /api/dashboard.php
 * Strictly scoped to authenticated admin's restaurant_id
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json; charset=utf-8');

// Ensure admin is logged in
if (empty($_SESSION['admin_logged_in']) || empty($_SESSION['admin_restaurant_id'])) {
    json_response(['success' => false, 'error' => 'Unauthorized access.'], 401);
}

$restaurantId = (int)$_SESSION['admin_restaurant_id'];

try {
    $db = get_db();

    // Calculate Today's Range in IST
    $todayStart = date('Y-m-d 00:00:00');
    $todayEnd   = date('Y-m-d 23:59:59');

    // 1. Today's Total Customers
    $stmt = $db->prepare("SELECT COUNT(*) AS total_today FROM customers WHERE restaurant_id = ? AND created_at BETWEEN ? AND ?");
    $stmt->execute([$restaurantId, $todayStart, $todayEnd]);
    $todayCustomers = (int)($stmt->fetch()['total_today'] ?? 0);

    // 2. Today's Local Customers
    $stmt = $db->prepare("SELECT COUNT(*) AS local_today FROM customers WHERE restaurant_id = ? AND customer_type = 'Local' AND created_at BETWEEN ? AND ?");
    $stmt->execute([$restaurantId, $todayStart, $todayEnd]);
    $todayLocal = (int)($stmt->fetch()['local_today'] ?? 0);

    // 3. Today's Outsider Customers
    $stmt = $db->prepare("SELECT COUNT(*) AS outsider_today FROM customers WHERE restaurant_id = ? AND customer_type = 'Outsider' AND created_at BETWEEN ? AND ?");
    $stmt->execute([$restaurantId, $todayStart, $todayEnd]);
    $todayOutsiders = (int)($stmt->fetch()['outsider_today'] ?? 0);

    // 4. Today's Total Bill Amount
    $stmt = $db->prepare("SELECT COALESCE(SUM(bill_amount), 0) AS bill_today FROM customers WHERE restaurant_id = ? AND created_at BETWEEN ? AND ?");
    $stmt->execute([$restaurantId, $todayStart, $todayEnd]);
    $todayBillAmount = (float)($stmt->fetch()['bill_today'] ?? 0.0);

    // 5. All-Time Total Customers
    $stmt = $db->prepare("SELECT COUNT(*) AS all_time FROM customers WHERE restaurant_id = ?");
    $stmt->execute([$restaurantId]);
    $totalCustomers = (int)($stmt->fetch()['all_time'] ?? 0);

    // 6. Marketing Consent Stats
    $stmt = $db->prepare("SELECT COUNT(*) AS consent_count FROM customers WHERE restaurant_id = ? AND marketing_consent = 1");
    $stmt->execute([$restaurantId]);
    $consentCount = (int)($stmt->fetch()['consent_count'] ?? 0);
    $consentPercentage = $totalCustomers > 0 ? round(($consentCount / $totalCustomers) * 100, 1) : 0;

    // 7. Recent 10 Customers
    $stmt = $db->prepare("SELECT id, name, mobile, customer_type, bill_amount, marketing_consent, created_at 
                          FROM customers 
                          WHERE restaurant_id = ? 
                          ORDER BY created_at DESC 
                          LIMIT 10");
    $stmt->execute([$restaurantId]);
    $recentCustomers = $stmt->fetchAll();

    // Format fields for frontend display
    $formattedRecent = array_map(function ($c) {
        return [
            'id'                => (int)$c['id'],
            'name'              => $c['name'],
            'mobile'            => $c['mobile'],
            'masked_mobile'     => mask_mobile($c['mobile']),
            'customer_type'     => $c['customer_type'],
            'bill_amount'       => (float)$c['bill_amount'],
            'formatted_amount'  => format_currency($c['bill_amount']),
            'marketing_consent' => (bool)$c['marketing_consent'],
            'created_at'        => $c['created_at'],
            'relative_time'     => format_relative_time($c['created_at']),
        ];
    }, $recentCustomers);

    json_response([
        'success' => true,
        'metrics' => [
            'today_customers'     => $todayCustomers,
            'today_local'         => $todayLocal,
            'today_outsiders'     => $todayOutsiders,
            'today_bill_amount'   => $todayBillAmount,
            'today_bill_currency' => format_currency($todayBillAmount),
            'total_customers'     => $totalCustomers,
            'consent_count'       => $consentCount,
            'consent_percentage'  => $consentPercentage,
        ],
        'recent_customers' => $formattedRecent,
    ]);

} catch (PDOException $e) {
    error_log("Error in api/dashboard.php: " . $e->getMessage());
    json_response(['success' => false, 'error' => 'Could not fetch dashboard metrics.'], 500);
}
