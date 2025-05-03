<?php
    session_start();
    include_once "config.php";
    $outgoing_id = $_SESSION['unique_id'];
    
    // Get users
    $users_sql = "SELECT * FROM users WHERE NOT unique_id = {$outgoing_id} ORDER BY user_id DESC";
    $users_query = mysqli_query($conn, $users_sql);
    
    // Get groups the user is a member of
    $groups_sql = "SELECT g.*, gm.is_admin 
                   FROM groups g 
                   INNER JOIN group_members gm ON g.group_id = gm.group_id 
                   WHERE gm.unique_id = {$outgoing_id}
                   ORDER BY g.created_at DESC";
    $groups_query = mysqli_query($conn, $groups_sql);
    
    $output = "";
    
    // First add groups
    if(mysqli_num_rows($groups_query) > 0){
        while($group = mysqli_fetch_assoc($groups_query)){
            // Get last message in group
            $last_msg_sql = "SELECT * FROM group_messages 
                            WHERE group_id = {$group['group_id']} 
                            ORDER BY msg_id DESC LIMIT 1";
            $last_msg_query = mysqli_query($conn, $last_msg_sql);
            $last_msg = mysqli_fetch_assoc($last_msg_query);
            
            $last_message = "";
            $time = "";
            if(mysqli_num_rows($last_msg_query) > 0){
                $last_message = $last_msg['message'];
                if(strlen($last_message) > 28){
                    $last_message = substr($last_message, 0, 28) . '...';
                }
                if(isset($last_msg['created_at'])){
                    $time = date("h:i A", strtotime($last_msg['created_at']));
                }
            }
            
            $output .= '<a href="group_chat.php?group_id='. $group['group_id'] .'" class="user">
                        <div class="content">
                            <img src="php/images/'. $group['group_image'] .'" alt="">
                            <div class="details">
                                <span>'. $group['name'] .'</span>
                                <p>'. $last_message .'</p>
                            </div>
                        </div>
                        <div class="meta">
                            <span class="time">'. $time .'</span>
                            '. ($group['is_admin'] ? '<span class="admin-badge">Admin</span>' : '') .'
                        </div>
                    </a>';
        }
    }
    
    // Then add individual users
    if(mysqli_num_rows($users_query) > 0){
        while($row = mysqli_fetch_assoc($users_query)){
            // Get last message between users
            $sql2 = "SELECT * FROM messages WHERE (incoming_msg_id = {$row['unique_id']}
                    OR outgoing_msg_id = {$row['unique_id']}) AND (outgoing_msg_id = {$outgoing_id} 
                    OR incoming_msg_id = {$outgoing_id}) ORDER BY msg_id DESC LIMIT 1";
            $query2 = mysqli_query($conn, $sql2);
            $row2 = mysqli_fetch_assoc($query2);
            
            $last_message = "";
            $time = "";
            if(mysqli_num_rows($query2) > 0){
                $last_message = $row2['msg'];
                if(strlen($last_message) > 28){
                    $last_message = substr($last_message, 0, 28) . '...';
                }
                if(isset($row2['created_at'])){
                    $time = date("h:i A", strtotime($row2['created_at']));
                }
            }

            $status_class = ($row['status'] == "Offline now") ? "offline" : "";
            $output .= '<a href="chat.php?user_id='. $row['unique_id'] .'" class="user">
                        <div class="content">
                            <img src="php/images/'. $row['img'] .'" alt="">
                            <div class="details">
                                <span>'. $row['fname']. " " . $row['lname'] .'</span>
                                <p>'. $last_message .'</p>
                            </div>
                        </div>
                        <div class="meta">
                            <span class="time">'. $time .'</span>
                            <span class="status-dot '. $status_class .'"></span>
                        </div>
                    </a>';
        }
    }
    
    if(empty($output)){
        $output .= "No users or groups available";
    }
    
    echo $output;
?>