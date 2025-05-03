<?php 
  session_start();
  include_once "php/config.php";
  if(!isset($_SESSION['unique_id'])){
    header("location: login.php");
  }
  
  // Check if user_id is set
  if(!isset($_GET['user_id'])) {
    header("location: users.php");
    exit;
  }
?>
<?php include_once "header.php"; ?>
<body>
  <div class="wrapper">
    <section class="users">
      <header>
        <div class="content">
          <?php 
            $sql = mysqli_query($conn, "SELECT * FROM users WHERE unique_id = {$_SESSION['unique_id']}");
            if(mysqli_num_rows($sql) > 0){
              $row = mysqli_fetch_assoc($sql);
            }
          ?>
          <img src="php/images/<?php echo $row['img']; ?>" alt="">
          <div class="details">
            <span><?php echo $row['fname']. " " . $row['lname'] ?></span>
            <p><?php echo $row['status']; ?></p>
          </div>
        </div>
        <div class="header-actions">
          <a href="create_group.php" class="create-group"><i class="fas fa-users"></i></a>
          <a href="settings.php" class="settings"><i class="fas fa-cog"></i></a>
          <a href="php/logout.php?logout_id=<?php echo $row['unique_id']; ?>" class="logout">Logout</a>
        </div>
      </header>
      <div class="search">
        <span class="text">Select an user to start chat</span>
        <input type="text" placeholder="Enter name to search...">
        <button><i class="fas fa-search"></i></button>
      </div>
      <div class="users-list">
      </div>
    </section>

    <section class="chat-area">
      <header>
        <?php 
          $user_id = mysqli_real_escape_string($conn, $_GET['user_id']);
          $chat_user_sql = mysqli_query($conn, "SELECT * FROM users WHERE unique_id = '{$user_id}'");
          if(mysqli_num_rows($chat_user_sql) > 0){
            $chat_user = mysqli_fetch_assoc($chat_user_sql);
          } else {
            header("location: users.php");
            exit;
          }
        ?>
        <img src="php/images/<?php echo $chat_user['img']; ?>" alt="">
        <div class="details">
          <span><?php echo $chat_user['fname']. " " . $chat_user['lname'] ?></span>
          <p><?php echo $chat_user['status']; ?></p>
        </div>
        <div class="call-controls" id="call-controls">
          <button id="start-audio-call" class="call-btn"><i class="fas fa-phone"></i></button>
          <button id="start-video-call" class="call-btn"><i class="fas fa-video"></i></button>
          <button id="end-call-btn" class="call-btn" style="display: none;"><i class="fas fa-phone-slash"></i></button>
        </div>
      </header>

      <!-- Video call elements -->
      <div class="video-container" id="video-container" style="display: none;">
        <video id="local-video" autoplay muted playsinline></video>
        <video id="remote-video" autoplay playsinline></video>
      </div>

      <div class="chat-box">
      </div>
      <form action="#" class="typing-area">
        <input type="text" class="incoming_id" name="incoming_id" value="<?php echo $user_id; ?>" hidden>
        <button type="button" class="emoji-button"><i class="far fa-smile"></i></button>
        <input type="text" name="message" class="input-field" placeholder="Type a message here..." autocomplete="off">
        <button class="send-btn"><i class="fab fa-telegram-plane"></i></button>
      </form>
    </section>
  </div>

  <style>
    /* Add these styles */
    .typing-area {
      padding: 18px 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 10px;
    }

    .typing-area input {
      height: 45px;
      width: calc(100% - 100px);
      font-size: 16px;
      padding: 0 13px;
      border: 1px solid #e6e6e6;
      outline: none;
      border-radius: 5px;
      background: #fff;
    }

    .typing-area button {
      color: #fff;
      width: 45px;
      height: 45px;
      border: none;
      outline: none;
      border-radius: 5px;
      background: #5B69C2;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.3s ease;
    }

    .typing-area button:hover {
      background: #4a57a5;
    }

    .emoji-button {
      background: #f0f2f5 !important;
      color: #707070 !important;
    }

    .emoji-button:hover {
      background: #e4e6e9 !important;
    }
  </style>

  <script>
    const currentUserId = <?php echo $_SESSION['unique_id']; ?>;
    const selectedUserId = <?php echo $user_id; ?>;
  </script>
  <script src="javascript/user-emoji-picker.js"></script>
  <script src="javascript/chat.js"></script>
  <script src="javascript/users.js"></script>
  <!-- <script src="javascript/webrtc.js"></script> -->
</body>
</html>
