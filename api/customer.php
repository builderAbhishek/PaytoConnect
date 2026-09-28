<?php
/**
 * SMART QR - Customer Data Ingestion & UPI Intent Generation API
 * Developed by BCA Students | Siddharth Shikshan Avam Prashikshan Sansthan
 * Mentored by Abhishek Chaudhary | ALAG INNOVATIVE SOLUTIONS
 *
 * Captures customer details BEFORE generating the UPI deep link.
 * POST /api/customer.php
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json; charset=utf-8');

// Accept only POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'error' => 'Only POST requests are permitted.'], 405);
}

// Support both JSON body and standard Form POST
$rawInput = file_get_contents('php://input');
$jsonData = json_decode($rawInput, true);

$data = is_array($jsonData) ? $jsonData : $_POST;

// 1. Resolve and Validate Restaurant
$restaurantSlug = sanitize($data['restaurant_slug'] ?? ($data['slug'] ?? ''));
$restaurantId   = isset($data['restaurant_id']) ? (int)$data['restaurant_id'] : 0;

$restaurant = null;
if (!empty($restaurantSlug)) {
    $restaurant = get_restaurant_by_slug($restaurantSlug);
} elseif ($restaurantId > 0) {
    $restaurant = get_restaurant_by_id($restaurantId);
}

if (!$restaurant) {
    json_response([
        'success' => false,
        'error'   => 'Invalid or inactive restaurant outlet. Please scan the QR code again.'
    ], 400);
}

$restaurantId = (int)$restaurant['id'];

// 2. Validate Customer Name
$name = sanitize($data['name'] ?? '');
if (empty($name)) {
    json_response(['success' => false, 'error' => 'Please enter your name.'], 422);
}
if (mb_strlen($name) > 100) {
    json_response(['success' => false, 'error' => 'Name cannot exceed 100 characters.'], 422);
}

// 3. Validate Mobile Number (Indian 10-digit)
$rawMobile = sanitize($data['mobile'] ?? '');
$cleanMobile = clean_indian_mobile($rawMobile);

if (!is_valid_indian_mobile($cleanMobile)) {
    json_response([
        'success' => false,
        'error'   => 'Please enter a valid 10-digit Indian mobile number.'
    ], 422);
}

// 4. Validate Customer Type (Local vs Outsider)
$rawType = sanitize($data['customer_type'] ?? 'Local');
$customerType = (strcasecmp($rawType, 'Outsider') === 0) ? 'Outsider' : 'Local';

// 5. Validate Bill Amount
$rawAmount = $data['bill_amount'] ?? 0;
if (!is_numeric($rawAmount)) {
    json_response(['success' => false, 'error' => 'Please enter a valid numeric bill amount.'], 422);
}
$billAmount = round((float)$rawAmount, 2);

if ($billAmount <= 0) {
    json_response(['success' => false, 'error' => 'Bill amount must be greater than ₹0.'], 422);
}
if ($billAmount > 500000) {
    json_response(['success' => false, 'error' => 'Bill amount exceeds the standard UPI transaction limit of ₹5,00,000.'], 422);
}

// 6. Marketing Consent (Optional checkbox, 1 or 0)
$consentInput = $data['marketing_consent'] ?? 0;
$marketingConsent = ($consentInput == 1 || $consentInput === true || $consentInput === 'true' || $consentInput === 'on') ? 1 : 0;

// 7. Save to Database using Prepared Statements
try {
    $db = get_db();
    $sql = "INSERT INTO customers (restaurant_id, name, mobile, customer_type, bill_amount, marketing_consent, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([
        $restaurantId,
        $name,
        $cleanMobile,
        $customerType,
        $billAmount,
        $marketingConsent,
        date('Y-m-d H:i:s')
    ]);

    $customerId = (int)$db->lastInsertId();

    // 8. Generate Compliant UPI Intent Deep Link
    // Note: No claim of automated verification; payment is handled in customer's UPI app
    $upiIntent = build_upi_intent(
        $restaurant['upi_id'],
        $restaurant['name'],
        $billAmount,
        "Bill at " . $restaurant['name']
    );

    json_response([
        'success'         => true,
        'message'         => 'Customer data captured successfully. Redirecting to your UPI app...',
        'customer_id'     => $customerId,
        'upi_intent'      => $upiIntent,
        'restaurant_name' => $restaurant['name'],
        'bill_amount'     => $billAmount,
        'upi_id'          => $restaurant['upi_id']
    ], 201);

} catch (PDOException $e) {
    error_log("Database error in api/customer.php: " . $e->getMessage());
    json_response([
        'success' => false,
        'error'   => 'Could not record transaction. Please try again or pay directly at the counter.'
    ], 500);
}
