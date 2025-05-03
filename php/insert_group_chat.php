<?php 
    session_start();
    if(isset($_SESSION['unique_id'])){
        include_once "config.php";
        $group_id = mysqli_real_escape_string($conn, $_POST['group_id']);
        $sender_id = $_SESSION['unique_id'];
        $message = mysqli_real_escape_string($conn, $_POST['message']);
        if(!empty($message)){
            $sql = mysqli_query($conn, "INSERT INTO group_messages (group_id, sender_id, message)
                                      VALUES ({$group_id}, {$sender_id}, '{$message}')");
            if($sql){
                echo "success";
            }
        }
    }
?> 