<?php
    session_start();
    include_once "config.php";

    if(!isset($_SESSION['unique_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
        exit;
    }

    $group_id = mysqli_real_escape_string($conn, $_POST['group_id']);
    $user_id = mysqli_real_escape_string($conn, $_POST['user_id']);
    $admin_id = $_SESSION['unique_id'];

    // Check if the current user is an admin of the group
    $admin_check = mysqli_query($conn, "SELECT * FROM group_members 
                                      WHERE group_id = {$group_id} 
                                      AND unique_id = {$admin_id} 
                                      AND is_admin = 1");
    
    if(mysqli_num_rows($admin_check) == 0) {
        echo json_encode(['status' => 'error', 'message' => 'You are not authorized to add members']);
        exit;
    }

    // Check if user is already a member
    $member_check = mysqli_query($conn, "SELECT * FROM group_members 
                                       WHERE group_id = {$group_id} 
                                       AND unique_id = {$user_id}");
    
    if(mysqli_num_rows($member_check) > 0) {
        echo json_encode(['status' => 'error', 'message' => 'User is already a member of this group']);
        exit;
    }

    // Add the new member
    $sql = mysqli_query($conn, "INSERT INTO group_members (group_id, unique_id, is_admin) 
                               VALUES ({$group_id}, {$user_id}, 0)");
    
    if($sql) {
        echo json_encode(['status' => 'success', 'message' => 'Member added successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to add member']);
    }
?> 