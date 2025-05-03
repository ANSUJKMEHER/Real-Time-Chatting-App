<?php
    session_start();
    include_once "config.php";

    if(!isset($_SESSION['unique_id'])) {
        exit;
    }

    $group_id = mysqli_real_escape_string($conn, $_GET['group_id']);
    
    $members_sql = mysqli_query($conn, "SELECT u.*, gm.is_admin 
                                      FROM users u 
                                      INNER JOIN group_members gm ON u.unique_id = gm.unique_id 
                                      WHERE gm.group_id = {$group_id}
                                      ORDER BY gm.is_admin DESC, u.fname ASC");
    
    $output = "";
    
    while($member = mysqli_fetch_assoc($members_sql)){
        $status_class = ($member['status'] == "Active now") ? "online" : "offline";
        $output .= '<div class="member">
                    <div class="member-info">
                        <img src="php/images/'. $member['img'] .'" alt="">
                        <div class="member-details">
                            <span>'. $member['fname'] . " " . $member['lname'] .'</span>
                            <p class="'. $status_class .'">'. ($member['is_admin'] ? 'Admin' : 'Member') .'</p>
                        </div>
                    </div>
                </div>';
    }

    echo $output;
?> 