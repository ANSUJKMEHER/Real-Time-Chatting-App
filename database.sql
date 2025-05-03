
CREATE DATABASE IF NOT EXISTS chatapp;
USE chatapp;


CREATE TABLE IF NOT EXISTS `users` (
    `user_id` int(11) NOT NULL AUTO_INCREMENT,
    `unique_id` int(200) NOT NULL,
    `fname` varchar(255) NOT NULL,
    `lname` varchar(255) NOT NULL,
    `email` varchar(255) NOT NULL,
    `password` varchar(255) NOT NULL,
    `img` varchar(400) NOT NULL,
    `status` varchar(255) NOT NULL,
    PRIMARY KEY (`user_id`),
    UNIQUE KEY `unique_id` (`unique_id`),
    UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE IF NOT EXISTS `messages` (
    `msg_id` int(11) NOT NULL AUTO_INCREMENT,
    `incoming_msg_id` int(255) NOT NULL,
    `outgoing_msg_id` int(255) NOT NULL,
    `msg` text NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`msg_id`),
    KEY `messages_users_incoming` (`incoming_msg_id`),
    KEY `messages_users_outgoing` (`outgoing_msg_id`),
    CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`incoming_msg_id`) REFERENCES `users` (`unique_id`) ON DELETE CASCADE,
    CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`outgoing_msg_id`) REFERENCES `users` (`unique_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE IF NOT EXISTS `groups` (
    `group_id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `group_image` varchar(255) NOT NULL DEFAULT 'default.png',
    `created_by` int(255) NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`group_id`),
    KEY `group_creator` (`created_by`),
    CONSTRAINT `groups_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`unique_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE IF NOT EXISTS `group_members` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `group_id` int(11) NOT NULL,
    `unique_id` int(255) NOT NULL,
    `is_admin` tinyint(1) NOT NULL DEFAULT 0,
    `joined_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_member` (`group_id`, `unique_id`),
    KEY `group_members_user` (`unique_id`),
    CONSTRAINT `group_members_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`group_id`) ON DELETE CASCADE,
    CONSTRAINT `group_members_ibfk_2` FOREIGN KEY (`unique_id`) REFERENCES `users` (`unique_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE IF NOT EXISTS `group_messages` (
    `msg_id` int(11) NOT NULL AUTO_INCREMENT,
    `group_id` int(11) NOT NULL,
    `sender_id` int(255) NOT NULL,
    `message` text NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`msg_id`),
    KEY `group_messages_group` (`group_id`),
    KEY `group_messages_sender` (`sender_id`),
    CONSTRAINT `group_messages_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`group_id`) ON DELETE CASCADE,
    CONSTRAINT `group_messages_ibfk_2` FOREIGN KEY (`sender_id`) REFERENCES `users` (`unique_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE INDEX idx_user_status ON users(status);
CREATE INDEX idx_messages_timestamp ON messages(created_at);
CREATE INDEX idx_group_messages_timestamp ON group_messages(created_at);


DELIMITER //
CREATE TRIGGER before_user_delete 
BEFORE DELETE ON users
FOR EACH ROW
BEGIN
   
    DELETE FROM messages WHERE incoming_msg_id = OLD.unique_id OR outgoing_msg_id = OLD.unique_id;
  
    DELETE FROM group_members WHERE unique_id = OLD.unique_id;
   
    DELETE FROM group_messages WHERE sender_id = OLD.unique_id;
END;//
DELIMITER ; 