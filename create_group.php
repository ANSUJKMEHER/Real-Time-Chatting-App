<?php 
  session_start();
  include_once "php/config.php";
  if(!isset($_SESSION['unique_id'])){
    header("location: login.php");
  }
?>
<?php include_once "header.php"; ?>
<body>
  <div class="wrapper">
    <section class="create-group-section">
      <header>
        <a href="chat.php" class="back-icon"><i class="fas fa-arrow-left"></i></a>
        <span class="title">Create New Group</span>
      </header>
      <div class="create-group-content">
        <form action="php/create_group.php" method="POST" enctype="multipart/form-data" class="group-form">
          <div class="group-details">
            <div class="field">
              <label>Group Name</label>
              <input type="text" name="group_name" placeholder="Enter group name" required>
            </div>
            <div class="field">
              <label>Group Image (Optional)</label>
              <input type="file" name="group_image" accept="image/x-png,image/gif,image/jpeg,image/jpg">
            </div>
          </div>
          
          <div class="select-members">
            <label>Select Members</label>
            <div class="users-list">
              <?php
                $current_user = $_SESSION['unique_id'];
                $sql = mysqli_query($conn, "SELECT * FROM users WHERE NOT unique_id = {$current_user}");
                if(mysqli_num_rows($sql) > 0) {
                  while($row = mysqli_fetch_assoc($sql)) {
              ?>
              <div class="user-item">
                <label class="user-select">
                  <input type="checkbox" name="members[]" value="<?php echo $row['unique_id']; ?>">
                  <div class="user-info">
                    <img src="php/images/<?php echo $row['img']; ?>" alt="">
                    <div class="details">
                      <span><?php echo $row['fname']. " " . $row['lname'] ?></span>
                      <p><?php echo $row['status']; ?></p>
                    </div>
                  </div>
                </label>
              </div>
              <?php
                  }
                }
              ?>
            </div>
          </div>
          
          <div class="field button">
            <input type="submit" value="Create Group">
          </div>
        </form>
      </div>
    </section>
  </div>

  <style>
    .create-group-section {
      background: #fff;
      width: 450px;
      border-radius: 16px;
      box-shadow: 0 0 128px 0 rgba(0,0,0,0.1),
                  0 32px 64px -48px rgba(0,0,0,0.5);
    }
    .create-group-section header {
      padding: 25px 30px;
      border-bottom: 1px solid #ddd;
      display: flex;
      align-items: center;
    }
    .create-group-section header .back-icon {
      font-size: 18px;
      color: #333;
      margin-right: 15px;
    }
    .create-group-section header .title {
      font-size: 20px;
      font-weight: 600;
      color: #333;
    }
    .create-group-content {
      padding: 30px;
    }
    .group-details .field {
      margin-bottom: 20px;
    }
    .group-details .field label {
      display: block;
      margin-bottom: 8px;
      font-size: 15px;
      color: #333;
    }
    .group-details .field input[type="text"] {
      width: 100%;
      height: 45px;
      padding: 0 15px;
      border: 1px solid #ddd;
      border-radius: 8px;
      font-size: 15px;
    }
    .group-details .field input[type="file"] {
      width: 100%;
      padding: 8px;
      border: 1px dashed #ddd;
      border-radius: 8px;
      background: #f8f9fa;
    }
    .select-members {
      margin-top: 30px;
    }
    .select-members label {
      display: block;
      margin-bottom: 15px;
      font-size: 15px;
      color: #333;
    }
    .users-list {
      max-height: 350px;
      overflow-y: auto;
    }
    .users-list::-webkit-scrollbar {
      width: 6px;
    }
    .users-list::-webkit-scrollbar-track {
      background: #f1f1f1;
      border-radius: 25px;
    }
    .users-list::-webkit-scrollbar-thumb {
      background: #ccc;
      border-radius: 25px;
    }
    .user-item {
      margin-bottom: 10px;
    }
    .user-select {
      display: flex;
      align-items: center;
      padding: 10px;
      border: 1px solid #eee;
      border-radius: 8px;
      cursor: pointer;
      transition: all 0.3s ease;
    }
    .user-select:hover {
      background: #f8f9fa;
    }
    .user-select input[type="checkbox"] {
      margin-right: 15px;
    }
    .user-info {
      display: flex;
      align-items: center;
    }
    .user-info img {
      height: 40px;
      width: 40px;
      border-radius: 50%;
      object-fit: cover;
      margin-right: 15px;
    }
    .user-info .details span {
      font-size: 16px;
      font-weight: 500;
      color: #333;
    }
    .user-info .details p {
      font-size: 13px;
      color: #667781;
    }
    .field.button {
      margin-top: 30px;
    }
    .field.button input {
      width: 100%;
      height: 45px;
      border: none;
      outline: none;
      background: #5B69C2;
      color: #fff;
      border-radius: 8px;
      font-size: 16px;
      cursor: pointer;
      transition: all 0.3s ease;
    }
    .field.button input:hover {
      background: #4a57a5;
    }
  </style>
</body>
</html> 