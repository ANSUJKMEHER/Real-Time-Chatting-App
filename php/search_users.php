<?php
    session_start();
    include_once "config.php";

    if(!isset($_SESSION['unique_id'])) {
        echo json_encode([]);
        exit;
    }

    $searchTerm = mysqli_real_escape_string($conn, $_GET['term']);
    $group_id = mysqli_real_escape_string($conn, $_GET['group_id']);
    $output = [];

    // Get users that match the search term and are not already in the group
    $sql = mysqli_query($conn, "SELECT * FROM users 
                               WHERE (fname LIKE '%{$searchTerm}%' OR lname LIKE '%{$searchTerm}%')
                               AND unique_id != {$_SESSION['unique_id']}
                               AND unique_id NOT IN (
                                   SELECT unique_id FROM group_members WHERE group_id = {$group_id}
                               )");
    
    if(mysqli_num_rows($sql) > 0) {
        while($row = mysqli_fetch_assoc($sql)) {
            $output[] = [
                'unique_id' => $row['unique_id'],
                'fname' => $row['fname'],
                'lname' => $row['lname'],
                'img' => $row['img']
            ];
        }
    }

    echo json_encode($output);
?> 