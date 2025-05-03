<?php
    session_start();
    include_once "config.php";

    if(!isset($_SESSION['unique_id'])){
        header("location: ../login.php");
    }

    if($_SERVER['REQUEST_METHOD'] == "POST"){
        $fname = mysqli_real_escape_string($conn, $_POST['fname']);
        $lname = mysqli_real_escape_string($conn, $_POST['lname']);
        
        // Update name
        if(!empty($fname) && !empty($lname)){
            $sql = mysqli_query($conn, "UPDATE users SET fname = '{$fname}', lname = '{$lname}' 
                                      WHERE unique_id = {$_SESSION['unique_id']}");
        }
        
        // Update profile photo
        if(isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK){
            $img_name = $_FILES['image']['name'];
            $img_type = $_FILES['image']['type'];
            $tmp_name = $_FILES['image']['tmp_name'];
            
            $img_explode = explode('.',$img_name);
            $img_ext = strtolower(end($img_explode));
    
            $extensions = ["jpeg", "png", "jpg"];
            if(in_array($img_ext, $extensions) === true){
                $types = ["image/jpeg", "image/jpg", "image/png"];
                if(in_array($img_type, $types) === true){
                    $time = time();
                    $new_img_name = $time.$img_name;
                    
                    // Make sure the images directory exists
                    if (!file_exists("../php/images")) {
                        mkdir("../php/images", 0777, true);
                    }
                    
                    $upload_path = "../php/images/".$new_img_name;
                    
                    if(move_uploaded_file($tmp_name, $upload_path)){
                        
                        $old_img_query = mysqli_query($conn, "SELECT img FROM users WHERE unique_id = {$_SESSION['unique_id']}");
                        $old_img = mysqli_fetch_assoc($old_img_query)['img'];
                        
                        
                        $sql2 = mysqli_query($conn, "UPDATE users SET img = '{$new_img_name}' 
                                                   WHERE unique_id = {$_SESSION['unique_id']}");
                                                   
                        if($sql2){
                            
                            if($old_img != "default.png" && file_exists("../php/images/".$old_img)){
                                unlink("../php/images/".$old_img);
                            }
                            header("location: ../settings.php?status=success");
                            exit;
                        } else {
                            header("location: ../settings.php?error=db_update_failed");
                            exit;
                        }
                    } else {
                        header("location: ../settings.php?error=upload_failed");
                        exit;
                    }
                } else {
                    header("location: ../settings.php?error=invalid_type");
                    exit;
                }
            } else {
                header("location: ../settings.php?error=invalid_extension");
                exit;
            }
        }
        
        header("location: ../settings.php?status=success");
        exit;
    }
?> 