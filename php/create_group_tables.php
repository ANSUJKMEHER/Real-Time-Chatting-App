<?php
include_once "config.php";

// Create group_messages table if it doesn't exist
$create_messages_table = "CREATE TABLE IF NOT EXISTS group_messages (
    msg_id INT PRIMARY KEY AUTO_INCREMENT,
    group_id INT NOT NULL,
    sender_id INT NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (group_id) REFERENCES `groups`(group_id) ON DELETE CASCADE,
    FOREIGN KEY (sender_id) REFERENCES users(unique_id) ON DELETE CASCADE
)";

if (mysqli_query($conn, $create_messages_table)) {
    echo "group_messages table created successfully or already exists<br>";
} else {
    echo "Error creating group_messages table: " . mysqli_error($conn) . "<br>";
}

// Check if the groups table exists
$check_groups = mysqli_query($conn, "SHOW TABLES LIKE 'groups'");
if (mysqli_num_rows($check_groups) == 0) {
    // Create groups table if it doesn't exist
    $create_groups_table = "CREATE TABLE IF NOT EXISTS `groups` (
        group_id INT PRIMARY KEY AUTO_INCREMENT,
        name VARCHAR(255) NOT NULL,
        group_image VARCHAR(255) NOT NULL DEFAULT 'default.png',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if (mysqli_query($conn, $create_groups_table)) {
        echo "groups table created successfully<br>";
    } else {
        echo "Error creating groups table: " . mysqli_error($conn) . "<br>";
    }
}

// Create group_members table if it doesn't exist
$create_members_table = "CREATE TABLE IF NOT EXISTS group_members (
    id INT PRIMARY KEY AUTO_INCREMENT,
    group_id INT NOT NULL,
    unique_id INT NOT NULL,
    is_admin BOOLEAN DEFAULT FALSE,
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (group_id) REFERENCES `groups`(group_id) ON DELETE CASCADE,
    FOREIGN KEY (unique_id) REFERENCES users(unique_id) ON DELETE CASCADE,
    UNIQUE KEY unique_member (group_id, unique_id)
)";

if (mysqli_query($conn, $create_members_table)) {
    echo "group_members table created successfully or already exists<br>";
} else {
    echo "Error creating group_members table: " . mysqli_error($conn) . "<br>";
}

?> 