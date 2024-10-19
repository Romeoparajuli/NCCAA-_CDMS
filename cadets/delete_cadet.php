

<?php
require '../includes/db_connection.php';

// Check if user is logged in and has proper permissions
session_start();
if ($_SESSION['user_role'] !== 'Province Admin') {
    // Return a JSON response indicating unauthorized access
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'You are not authorized to delete this cadet.']);
    exit;
}

$cadet_id = $_GET['id'] ?? null;

// Initialize response array
$response = [];

if ($cadet_id) {
    // Prepare and execute the deletion statement
    $stmt = $pdo->prepare("DELETE FROM cadets WHERE id = ?");
    if ($stmt->execute([$cadet_id])) {
        // Successful deletion
        $response['success'] = true;
        $response['message'] = 'Cadet deleted successfully.';
    } else {
        // Error in deletion
        $response['success'] = false;
        $response['message'] = 'Error deleting cadet.';
    }
} else {
    // No cadet ID provided
    $response['success'] = false;
    $response['message'] = 'Cadet not found.';
}

// Set the content type to JSON and echo the response
header('Content-Type: application/json');
echo json_encode($response);
