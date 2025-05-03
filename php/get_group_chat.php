<?php 
    session_start();
    if(isset($_SESSION['unique_id'])){
        include_once "config.php";
        $group_id = mysqli_real_escape_string($conn, $_POST['group_id']);
        $output = "";
        
        $messages_sql = "SELECT gm.*, u.fname, u.lname, u.img 
                        FROM group_messages gm
                        INNER JOIN users u ON gm.sender_id = u.unique_id
                        WHERE gm.group_id = {$group_id} 
                        ORDER BY gm.msg_id";
        $messages_query = mysqli_query($conn, $messages_sql);
        
        if(mysqli_num_rows($messages_query) > 0){
            while($message = mysqli_fetch_assoc($messages_query)){
                if($message['sender_id'] === $_SESSION['unique_id']){
                    $output .= '<div class="chat outgoing">
                                <div class="details">
                                    <span class="name">You</span>
                                    <p>'. $message['message'] .'</p>
                                </div>
                                </div>';
                }else{
                    $output .= '<div class="chat incoming">
                                <img src="php/images/'.$message['img'].'" alt="">
                                <div class="details">
                                    <span class="name">'.$message['fname'].' '.$message['lname'].'</span>
                                    <p>'. $message['message'] .'</p>
                                </div>
                                </div>';
                }
            }
        }else{
            $output .= '<div class="text">No messages are available. Once you send message they will appear here.</div>';
        }
        echo $output;
    }
?> 