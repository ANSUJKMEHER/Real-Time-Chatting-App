<?php
    session_start();
    include_once "config.php";

    if(isset($_SESSION['unique_id'])) {
        $sql = mysqli_query($conn, "UPDATE users SET status = 'Active now' 
                                  WHERE unique_id = {$_SESSION['unique_id']}");
    }
?> 