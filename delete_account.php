<?php
session_start();
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['unique_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

// Include the database connection
include_once "config.php";

// Get the user's unique_id from session
$unique_id = mysqli_real_escape_string($conn, $_SESSION['unique_id']);

// Get the request data
$data = json_decode(file_get_contents('php://input'), true);

if ($data['action'] !== 'delete_account') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
    exit;
}

// Begin transaction
mysqli_begin_transaction($conn);

try {
    // Delete user's messages
    $sql = "DELETE FROM messages WHERE incoming_msg_id = {$unique_id} OR outgoing_msg_id = {$unique_id}";
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        throw new Exception("Error deleting messages");
    }

    // Delete user's profile image
    $sql = "SELECT img FROM users WHERE unique_id = {$unique_id}";
    $result = mysqli_query($conn, $sql);
    if ($result && $row = mysqli_fetch_assoc($result)) {
        $img_path = $row['img'];
        if (file_exists($img_path)) {
            unlink($img_path);
        }
    }

    // Delete user from users table
    $sql = "DELETE FROM users WHERE unique_id = {$unique_id}";
    $result = mysqli_query($conn, $sql);
    if (!$result) {
        throw new Exception("Error deleting user account");
    }

    // Commit transaction
    mysqli_commit($conn);
    
    echo json_encode(['status' => 'success']);
    
    // Clear session
    session_unset();
    session_destroy();
    
} catch (Exception $e) {
    // Rollback transaction on error
    mysqli_rollback($conn);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

// Close database connection
mysqli_close($conn);
?> 