<?php
session_start();
include_once "config.php";

if(!isset($_SESSION['unique_id'])) {
    header("location: ../login.php");
    exit;
}

if($_SERVER['REQUEST_METHOD'] == "POST") {
    $group_name = mysqli_real_escape_string($conn, $_POST['group_name']);
    $created_by = $_SESSION['unique_id'];
    $members = isset($_POST['members']) ? $_POST['members'] : [];
    
    if(empty($group_name)) {
        header("location: ../create_group.php?error=name_required");
        exit;
    }
    
    if(empty($members)) {
        header("location: ../create_group.php?error=members_required");
        exit;
    }
    
    // Handle group image upload
    $img_name = "default_group.png"; // Default image
    if(isset($_FILES['group_image'])) {
        $img = $_FILES['group_image'];
        if($img['error'] === 0) {
            $img_type = $img['type'];
            $allowed = ["image/jpeg", "image/jpg", "image/png"];
            
            if(in_array($img_type, $allowed)) {
                $time = time();
                $new_img_name = $time . $img['name'];
                $img_path = "../php/images/" . $new_img_name;
                
                if(!is_dir("../php/images")) {
                    mkdir("../php/images", 0777, true);
                }
                
                if(move_uploaded_file($img['tmp_name'], $img_path)) {
                    $img_name = $new_img_name;
                }
            }
        }
    }
    
    // Create the group
    $sql = mysqli_query($conn, "INSERT INTO groups (name, created_by, group_image) 
                               VALUES ('{$group_name}', {$created_by}, '{$img_name}')");
    
    if($sql) {
        $group_id = mysqli_insert_id($conn);
        
        // Add creator as admin
        $sql2 = mysqli_query($conn, "INSERT INTO group_members (group_id, unique_id, is_admin) 
                                    VALUES ({$group_id}, {$created_by}, 1)");
        
        // Add selected members
        foreach($members as $member_id) {
            $member_id = mysqli_real_escape_string($conn, $member_id);
            mysqli_query($conn, "INSERT INTO group_members (group_id, unique_id, is_admin) 
                               VALUES ({$group_id}, {$member_id}, 0)");
        }
        
        header("location: ../chat.php?group_created=success");
        exit;
    } else {
        header("location: ../create_group.php?error=db_error");
        exit;
    }
} else {
    header("location: ../create_group.php");
    exit;
}
?> 