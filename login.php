<?php 
  session_start();
  if(isset($_SESSION['unique_id'])){
    header("location: users.php");
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat App - Login</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css"/>
    <style>
        body {
            background: linear-gradient(135deg, #000000 0%, #1a1a1a 25%, #2d2d2d 50%, #1a1a1a 75%, #000000 100%);
            position: relative;
            overflow: hidden;
            margin: 0;
            padding: 0;
        }
        #stars-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }
        .star {
            position: absolute;
            width: 2px;
            height: 2px;
            background: #fff;
            border-radius: 50%;
            box-shadow: 0 0 5px #fff, 0 0 10px #fff;
            animation: twinkle 2s infinite;
        }
        .connection {
            position: absolute;
            height: 1px;
            background: rgba(255, 255, 255, 0.1);
            transform-origin: 0 0;
        }
        @keyframes twinkle {
            0%, 100% { opacity: 0.2; }
            50% { opacity: 1; }
        }
        .wrapper {
            background: transparent;
            box-shadow: none;
        }
        .form {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }
        .form header {
            color: #5B69C2;
            font-size: 2.5rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 30px;
            background: linear-gradient(45deg, #5B69C2, #4a57a5);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .form .field input {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        .form .field input:focus {
            border-color: #5B69C2;
            box-shadow: 0 0 0 2px rgba(91, 105, 194, 0.1);
        }
        .form .button input {
            background: linear-gradient(45deg, #5B69C2, #4a57a5);
            transition: all 0.3s ease;
        }
        .form .button input:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(91, 105, 194, 0.3);
        }
        .form .link a {
            color: #5B69C2;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .form .link a:hover {
            color: #4a57a5;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div id="stars-container"></div>
    <div class="wrapper">
        <section class="form login">
            <header>ECHOCHAT</header>
            <form action="#" method="POST" enctype="multipart/form-data" autocomplete="off">
                <div class="error-text"></div>
                <div class="field input">
                    <label>Email Address</label>
                    <input type="text" name="email" placeholder="Enter your email" required>
                </div>
                <div class="field input">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="Enter your password" required>
                    <i class="fas fa-eye"></i>
                </div>
                <div class="field button">
                    <input type="submit" name="submit" value="Continue to Chat">
                </div>
            </form>
            <div class="link">Not yet signed up? <a href="index.php">Signup now</a></div>
        </section>
    </div>

    <script>
        class StarField {
            constructor() {
                this.container = document.getElementById('stars-container');
                this.stars = [];
                this.connections = [];
                this.numStars = 100;
                this.maxDistance = 150;
                this.init();
            }

            init() {
                // Create stars
                for (let i = 0; i < this.numStars; i++) {
                    this.createStar();
                }

                // Animate
                this.animate();
            }

            createStar() {
                const star = document.createElement('div');
                star.className = 'star';
                
                // Random position
                const x = Math.random() * window.innerWidth;
                const y = Math.random() * window.innerHeight;
                
                // Random size
                const size = Math.random() * 2 + 1;
                
                // Random animation delay
                const delay = Math.random() * 2;
                
                star.style.left = `${x}px`;
                star.style.top = `${y}px`;
                star.style.width = `${size}px`;
                star.style.height = `${size}px`;
                star.style.animationDelay = `${delay}s`;
                
                this.container.appendChild(star);
                this.stars.push({ element: star, x, y });
            }

            createConnection(star1, star2) {
                const connection = document.createElement('div');
                connection.className = 'connection';
                
                const dx = star2.x - star1.x;
                const dy = star2.y - star1.y;
                const length = Math.sqrt(dx * dx + dy * dy);
                
                if (length > this.maxDistance) return null;
                
                const angle = Math.atan2(dy, dx) * 180 / Math.PI;
                
                connection.style.left = `${star1.x}px`;
                connection.style.top = `${star1.y}px`;
                connection.style.width = `${length}px`;
                connection.style.transform = `rotate(${angle}deg)`;
                connection.style.opacity = 1 - (length / this.maxDistance);
                
                this.container.appendChild(connection);
                return connection;
            }

            updateConnections() {
                // Remove old connections
                this.connections.forEach(conn => conn.remove());
                this.connections = [];

                // Create new connections
                for (let i = 0; i < this.stars.length; i++) {
                    for (let j = i + 1; j < this.stars.length; j++) {
                        const connection = this.createConnection(this.stars[i], this.stars[j]);
                        if (connection) {
                            this.connections.push(connection);
                        }
                    }
                }
            }

            animate() {
                // Move stars slightly
                this.stars.forEach(star => {
                    star.x += (Math.random() - 0.5) * 0.5;
                    star.y += (Math.random() - 0.5) * 0.5;
                    
                    // Keep stars within bounds
                    star.x = Math.max(0, Math.min(window.innerWidth, star.x));
                    star.y = Math.max(0, Math.min(window.innerHeight, star.y));
                    
                    star.element.style.left = `${star.x}px`;
                    star.element.style.top = `${star.y}px`;
                });

                // Update connections
                this.updateConnections();

                // Continue animation
                requestAnimationFrame(() => this.animate());
            }
        }

        // Initialize star field when the page loads
        window.addEventListener('load', () => {
            new StarField();
        });
    </script>

    <script src="javascript/pass-show-hide.js"></script>
    <script src="javascript/login.js"></script>
</body>
</html>
