<?php
session_start();
include_once "config.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $action = $data['action'] ?? '';
    $caller_id = $data['caller_id'] ?? '';
    $receiver_id = $data['receiver_id'] ?? '';
    $type = $data['type'] ?? '';
    $sdp = $data['sdp'] ?? '';
    $candidate = $data['candidate'] ?? '';
    $call_id = $data['call_id'] ?? '';

    switch ($action) {
        case 'initiate_call':
            $sql = "INSERT INTO call_sessions (caller_id, receiver_id, type) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iis", $caller_id, $receiver_id, $type);
            if ($stmt->execute()) {
                $call_id = $conn->insert_id;
                echo json_encode(['status' => 'success', 'call_id' => $call_id]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to initiate call']);
            }
            break;

        case 'answer_call':
            $sql = "UPDATE call_sessions SET status = 'accepted' WHERE call_id = ? AND receiver_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $call_id, $receiver_id);
            if ($stmt->execute()) {
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to answer call']);
            }
            break;

        case 'end_call':
            $sql = "UPDATE call_sessions SET status = 'ended', end_time = CURRENT_TIMESTAMP WHERE call_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $call_id);
            if ($stmt->execute()) {
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to end call']);
            }
            break;

        case 'reject_call':
            $sql = "UPDATE call_sessions SET status = 'rejected' WHERE call_id = ? AND receiver_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $call_id, $receiver_id);
            if ($stmt->execute()) {
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to reject call']);
            }
            break;

        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
            break;
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?> 