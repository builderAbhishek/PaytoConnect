<?php
/**
 * SMART QR - Restaurant Public Info API
 * Developed by BCA Students | Siddharth Shikshan Avam Prashikshan Sansthan
 * Mentored by Abhishek Chaudhary | ALAG INNOVATIVE SOLUTIONS
 *
 * GET /api/restaurant.php?slug=chaudhary
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    json_response(['success' => false, 'error' => 'Method not allowed'], 405);
}

$slug = sanitize($_GET['slug'] ?? '');
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (empty($slug) && $id <= 0) {
    json_response(['success' => false, 'error' => 'Restaurant slug or ID is required'], 400);
}

try {
    $restaurant = $slug ? get_restaurant_by_slug($slug) : get_restaurant_by_id($id);

    if (!$restaurant) {
        json_response([
            'success' => false,
            'error'   => 'Restaurant not found'
        ], 404);
    }

    // Return public-safe restaurant details
    json_response([
        'success'    => true,
        'restaurant' => [
            'id'          => (int)$restaurant['id'],
            'name'        => $restaurant['name'],
            'slug'        => $restaurant['slug'],
            'logo'        => $restaurant['logo'] ?: null,
            'address'     => $restaurant['address'],
            'phone'       => $restaurant['phone'],
            'upi_id'      => $restaurant['upi_id'],
            'description' => $restaurant['description'] ?: '',
        ]
    ]);
} catch (Exception $e) {
    error_log("API Error in restaurant.php: " . $e->getMessage());
    json_response(['success' => false, 'error' => 'An internal error occurred'], 500);
}
