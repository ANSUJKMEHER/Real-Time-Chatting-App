# Real-time Web Chat Application
Images :
![Screenshot 2025-04-23 130505](https://github.com/user-attachments/assets/94d1c83b-cf5c-404b-abca-f3c5aeed46f3)
![Screenshot 2025-04-23 130725](https://github.com/user-attachments/assets/04a6852e-d976-4636-b1af-b14a4fbbfe17)
![Screenshot 2025-04-23 130619](https://github.com/user-attachments/assets/b7662bc5-15d1-490e-8875-85119cd344c6)
![Screenshot 2025-04-23 125145](https://github.com/user-attachments/assets/78176bbb-0251-4406-958e-2ffb3807a084)
![Screenshot 2025-04-23 125305](https://github.com/user-attachments/assets/9463144f-2c38-4ab6-b854-8a27efbf5d36)
![Screenshot 2025-04-23 130113](https://github.com/user-attachments/assets/a5fdc219-6e7d-4c3a-af64-b3db496fdf67)
![Screenshot 2025-04-23 125425](https://github.com/user-attachments/assets/f9cc6532-f513-4470-9a1d-f6b08e09ca6a)





A full-featured web-based chat application that enables real-time communication between users, supporting both one-on-one and group conversations.

## Features

- 🔐 **Secure Authentication**
  - User registration and login system
  - Password protection and session management
  - Account settings and profile management

- 💬 **Real-time Messaging**
  - One-on-one private chats
  - Group chat functionality
  - Real-time message updates
  - Message history

- 👥 **Group Management**
  - Create and join groups
  - Group admin controls
  - Member management
  - Group settings

- 🎨 **User Interface**
  - Modern and responsive design
  - Intuitive navigation
  - Customizable user profiles
  - Mobile-friendly interface

## Tech Stack

- **Frontend:**
  - HTML5
  - CSS3
  - JavaScript
  - jQuery

- **Backend:**
  - PHP
  - MySQL
  - Composer (Dependency Management)

## Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- Composer (for dependency management)

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/ANSUJKMEHER/Real-Time-Chatting-App.git
   ```

2. Navigate to the project directory:
   ```bash
   cd Real-Time-Chatting-App
   ```

3. Install dependencies:
   ```bash
   composer install
   ```

4. Import the database:
   - Use the provided `database.sql` file to create the necessary database structure
   - You can import it using phpMyAdmin or MySQL command line

5. Configure the database connection:
   - Open `config.php`
   - Update the database credentials with your local settings

6. Start your local server:
   - If using XAMPP, place the project in the `htdocs` directory
   - Access the application through your web browser

## Configuration

1. Database Configuration:
   - Edit `config.php` to set your database credentials
   - Ensure the database name matches your local setup

2. Server Configuration:
   - Make sure your web server has PHP and MySQL extensions enabled
   - Configure proper file permissions for the project directory

## Usage

1. Register a new account or login with existing credentials
2. Start chatting with other users
3. Create or join groups for group conversations
4. Customize your profile and settings

## Security Features

- Password hashing
- Session management
- Input validation
- SQL injection prevention
- XSS protection

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.


## Contact

Your Name - ansujkmeher@gmail.com

