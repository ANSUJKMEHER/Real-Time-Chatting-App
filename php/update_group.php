<?php
session_start();
include_once "config.php";

if(!isset($_SESSION['unique_id'])) {
    header("location: ../login.php");
    exit;
}

$group_id = mysqli_real_escape_string($conn, $_POST['group_id']);
$user_id = $_SESSION['unique_id'];

// Verify user is admin
$admin_check = mysqli_query($conn, "SELECT * FROM group_members 
                                  WHERE group_id = {$group_id} 
                                  AND unique_id = {$user_id} 
                                  AND is_admin = 1");
if(mysqli_num_rows($admin_check) == 0) {
    header("location: ../users.php");
    exit;
}

$action = mysqli_real_escape_string($conn, $_POST['action']);

switch($action) {
    case 'update_image':
        if(isset($_FILES['group_image'])){
            $img_name = $_FILES['group_image']['name'];
            $img_type = $_FILES['group_image']['type'];
            $tmp_name = $_FILES['group_image']['tmp_name'];
            
            $img_explode = explode('.',$img_name);
            $img_ext = end($img_explode);
    
            $extensions = ["jpeg", "png", "jpg"];
            if(in_array($img_ext, $extensions) === true){
                $types = ["image/jpeg", "image/jpg", "image/png"];
                if(in_array($img_type, $types) === true){
                    $time = time();
                    $new_img_name = $time.$img_name;
                    
                    // Get old image name to delete
                    $old_image_query = mysqli_query($conn, "SELECT group_image FROM groups WHERE group_id = {$group_id}");
                    $old_image = mysqli_fetch_assoc($old_image_query)['group_image'];
                    
                    if(move_uploaded_file($tmp_name,"../php/images/".$new_img_name)){
                        // Delete old image if it exists and is not the default image
                        if($old_image != "default.png" && file_exists("images/".$old_image)) {
                            unlink("images/".$old_image);
                        }
                        
                        $sql = mysqli_query($conn, "UPDATE groups SET group_image = '{$new_img_name}' 
                                                  WHERE group_id = {$group_id}");
                        if($sql){
                            header("location: ../manage_group.php?group_id=".$group_id."&msg=Image updated successfully");
                        }
                    }
                }else{
                    header("location: ../manage_group.php?group_id=".$group_id."&error=Please upload an image file - jpeg, png, jpg");
                }
            }else{
                header("location: ../manage_group.php?group_id=".$group_id."&error=Please upload an image file - jpeg, png, jpg");
            }
        }
        break;

    case 'update_name':
        $new_name = mysqli_real_escape_string($conn, $_POST['group_name']);
        if(!empty($new_name)){
            $sql = mysqli_query($conn, "UPDATE groups SET name = '{$new_name}' 
                                      WHERE group_id = {$group_id}");
            if($sql){
                header("location: ../manage_group.php?group_id=".$group_id."&msg=Name updated successfully");
            }
        }
        break;

    case 'delete_group':
        // First delete all messages
        mysqli_query($conn, "DELETE FROM group_messages WHERE group_id = {$group_id}");
        
        // Delete all member associations
        mysqli_query($conn, "DELETE FROM group_members WHERE group_id = {$group_id}");
        
        // Get group image to delete
        $image_query = mysqli_query($conn, "SELECT group_image FROM groups WHERE group_id = {$group_id}");
        $group_image = mysqli_fetch_assoc($image_query)['group_image'];
        
        // Delete the group
        $delete_query = mysqli_query($conn, "DELETE FROM groups WHERE group_id = {$group_id}");
        
        if($delete_query){
            // Delete group image if it's not the default
            if($group_image != "default.png" && file_exists("images/".$group_image)) {
                unlink("images/".$group_image);
            }
            header("location: ../users.php?msg=Group deleted successfully");
        }
        break;

    default:
        header("location: ../users.php");
        break;
}
?> 