<?php 
  session_start();
  include_once "php/config.php";
  if(!isset($_SESSION['unique_id'])){
    header("location: login.php");
  }
  
  // Check if group_id is set
  if(!isset($_GET['group_id'])) {
    header("location: users.php");
    exit;
  }

  // Check if user is an admin of this group
  $group_id = mysqli_real_escape_string($conn, $_GET['group_id']);
  $user_id = $_SESSION['unique_id'];
  
  $admin_check = mysqli_query($conn, "SELECT g.* FROM groups g 
                                    INNER JOIN group_members gm ON g.group_id = gm.group_id 
                                    WHERE g.group_id = {$group_id} 
                                    AND gm.unique_id = {$user_id} 
                                    AND gm.is_admin = 1");
  if(mysqli_num_rows($admin_check) == 0) {
    header("location: users.php");
    exit;
  }

  $group = mysqli_fetch_assoc($admin_check);
?>
<?php include_once "header.php"; ?>
<body>
  <div class="wrapper">
    <section class="manage-group">
      <header>
        <a href="group_chat.php?group_id=<?php echo $group_id; ?>" class="back-icon"><i class="fas fa-arrow-left"></i></a>
        <span class="title">Manage Group</span>
      </header>
      
      <div class="group-settings">
        <!-- Update Group Image Form -->
        <form action="php/update_group.php" method="POST" enctype="multipart/form-data" class="setting-form">
          <input type="hidden" name="group_id" value="<?php echo $group_id; ?>">
          <input type="hidden" name="action" value="update_image">
          <div class="field">
            <label>Group Image</label>
            <div class="current-image">
              <img src="php/images/<?php echo $group['group_image']; ?>" alt="">
            </div>
            <input type="file" name="group_image" accept="image/x-png,image/gif,image/jpeg,image/jpg" required>
            <button type="submit">Update Group Image</button>
          </div>
        </form>

        <!-- Update Group Name Form -->
        <form action="php/update_group.php" method="POST" class="setting-form">
          <input type="hidden" name="group_id" value="<?php echo $group_id; ?>">
          <input type="hidden" name="action" value="update_name">
          <div class="field">
            <label>Group Name</label>
            <input type="text" name="group_name" value="<?php echo $group['name']; ?>" required>
            <button type="submit">Update Group Name</button>
          </div>
        </form>

        <!-- Delete Group Form -->
        <form action="php/update_group.php" method="POST" class="setting-form delete-form" onsubmit="return confirm('Are you sure you want to delete this group? This action cannot be undone.');">
          <input type="hidden" name="group_id" value="<?php echo $group_id; ?>">
          <input type="hidden" name="action" value="delete_group">
          <div class="field">
            <button type="submit" class="delete-btn">Delete Group</button>
          </div>
        </form>
      </div>
    </section>
  </div>

  <style>
    .manage-group {
      background: #fff;
      width: 450px;
      border-radius: 16px;
      box-shadow: 0 0 128px 0 rgba(0,0,0,0.1),
                  0 32px 64px -48px rgba(0,0,0,0.5);
    }

    .manage-group header {
      padding: 25px 30px;
      border-bottom: 1px solid #ddd;
      display: flex;
      align-items: center;
    }

    .manage-group header .back-icon {
      font-size: 18px;
      color: #333;
      margin-right: 15px;
    }

    .manage-group header .title {
      font-size: 20px;
      font-weight: 600;
      color: #333;
    }

    .group-settings {
      padding: 30px;
    }

    .setting-form {
      margin-bottom: 30px;
      padding-bottom: 30px;
      border-bottom: 1px solid #eee;
    }

    .setting-form:last-child {
      margin-bottom: 0;
      padding-bottom: 0;
      border-bottom: none;
    }

    .field {
      margin-bottom: 15px;
    }

    .field label {
      display: block;
      margin-bottom: 10px;
      font-size: 15px;
      color: #333;
    }

    .field input[type="text"],
    .field input[type="file"] {
      width: 100%;
      padding: 10px;
      border: 1px solid #ddd;
      border-radius: 8px;
      font-size: 15px;
      margin-bottom: 10px;
    }

    .current-image {
      width: 100px;
      height: 100px;
      margin-bottom: 15px;
    }

    .current-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      border-radius: 50%;
    }

    .field button {
      width: 100%;
      padding: 10px;
      border: none;
      outline: none;
      background: #5B69C2;
      color: #fff;
      border-radius: 8px;
      font-size: 16px;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .field button:hover {
      background: #4a57a5;
    }

    .delete-form .field button {
      background: #dc3545;
    }

    .delete-form .field button:hover {
      background: #c82333;
    }
  </style>
</body>
</html> 