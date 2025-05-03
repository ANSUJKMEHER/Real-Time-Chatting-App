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
    <title>Echochat - Connect with People</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css"/>
    <style>
        .landing-container {
            text-align: center;
            padding: 50px 20px;
            color: white;
            position: relative;
            z-index: 1;
        }
        .landing-title {
            font-size: 4.5rem;
            font-weight: 800;
            margin-bottom: 20px;
            background: linear-gradient(45deg, #fff, #e0e0e0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 0 20px rgba(255,255,255,0.3);
        }
        .landing-description {
            font-size: 1.5rem;
            margin-bottom: 40px;
            color: #e0e0e0;
            font-weight: 300;
        }
        .landing-button {
            display: inline-block;
            padding: 15px 40px;
            font-size: 1.2rem;
            background: linear-gradient(45deg, #5B69C2, #4a57a5);
            color: white;
            text-decoration: none;
            border-radius: 30px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            margin-bottom: 40px;
            border: none;
            cursor: pointer;
        }
        .landing-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        .connect-text {
            font-size: 1.2rem;
            color: #a0a0a0;
            letter-spacing: 2px;
            text-transform: uppercase;
        }
        .landing-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #000000 0%, #1a1a1a 25%, #2d2d2d 50%, #1a1a1a 75%, #000000 100%);
            z-index: -1;
        }
        .landing-background::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at center, rgba(255,255,255,0.1) 0%, transparent 70%);
            pointer-events: none;
        }
    </style>
</head>
<body>
    <div class="landing-background"></div>
    <div class="landing-container">
        <h1 class="landing-title">Echochat</h1>
        <p class="landing-description">Experience real-time communication like never before</p>
        <a href="index.php" class="landing-button">Get Started</a>
        <p class="connect-text">Connect with people</p>
    </div>
</body>
</html> 