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
    <section class="settings-section">
      <header>
        <a href="chat.php" class="back-icon"><i class="fas fa-arrow-left"></i></a>
        <span class="title">Settings</span>
      </header>
      <div class="settings-content">
        <?php 
          $sql = mysqli_query($conn, "SELECT * FROM users WHERE unique_id = {$_SESSION['unique_id']}");
          if(mysqli_num_rows($sql) > 0){
            $row = mysqli_fetch_assoc($sql);
          }
        ?>
        <?php
          if(isset($_GET['error'])) {
            $error = '';
            switch($_GET['error']) {
              case 'upload_failed':
                $error = "Failed to upload image. Please try again.";
                break;
              case 'invalid_type':
                $error = "Invalid image type. Please use JPG, JPEG or PNG.";
                break;
              case 'invalid_extension':
                $error = "Invalid file extension. Please use JPG, JPEG or PNG.";
                break;
              case 'db_update_failed':
                $error = "Failed to update profile. Please try again.";
                break;
            }
            if($error) {
              echo '<div class="alert error">'.$error.'</div>';
            }
          }
          if(isset($_GET['status']) && $_GET['status'] == 'success') {
            echo '<div class="alert success">Profile updated successfully!</div>';
          }
        ?>
        <div class="profile-preview">
          <img src="php/images/<?php echo $row['img']; ?>" alt="">
          <h2><?php echo $row['fname']. " " . $row['lname'] ?></h2>
          <p><?php echo $row['email']; ?></p>
        </div>
        <div class="settings-options">
          <form action="php/update_profile.php" method="POST" enctype="multipart/form-data" class="update-form">
            <div class="option">
              <label>Update Profile Photo</label>
              <div class="field">
                <input type="file" name="image" accept="image/x-png,image/gif,image/jpeg,image/jpg">
              </div>
            </div>
            <div class="option">
              <label>Update Name</label>
              <div class="field">
                <input type="text" name="fname" placeholder="First Name" value="<?php echo $row['fname']; ?>">
                <input type="text" name="lname" placeholder="Last Name" value="<?php echo $row['lname']; ?>">
              </div>
            </div>
            <div class="button">
              <input type="submit" value="Save Changes">
            </div>
          </form>
          <div class="danger-zone">
            <h3>Danger Zone</h3>
            <div class="delete-account">
              <p>Once you delete your account, there is no going back. Please be certain.</p>
              <button id="deleteAccountBtn" class="delete-btn">Delete Account</button>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>

  <div id="deleteConfirmModal" class="modal">
    <div class="modal-content">
      <h2>Delete Account</h2>
      <p>Are you sure you want to delete your account? This action cannot be undone.</p>
      <div class="modal-buttons">
        <button id="confirmDelete" class="confirm-delete">Yes, Delete My Account</button>
        <button id="cancelDelete" class="cancel-delete">Cancel</button>
      </div>
    </div>
  </div>

  <style>
    .settings-section {
      background: #fff;
      width: 450px;
      border-radius: 16px;
      box-shadow: 0 0 128px 0 rgba(0,0,0,0.1),
                  0 32px 64px -48px rgba(0,0,0,0.5);
    }
    .settings-section header {
      padding: 25px 30px;
      border-bottom: 1px solid #ddd;
      display: flex;
      align-items: center;
    }
    .settings-section header .back-icon {
      font-size: 18px;
      color: #333;
      margin-right: 15px;
    }
    .settings-section header .title {
      font-size: 20px;
      font-weight: 600;
      color: #333;
    }
    .settings-content {
      padding: 30px;
    }
    .profile-preview {
      text-align: center;
      margin-bottom: 30px;
    }
    .profile-preview img {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      object-fit: cover;
      margin-bottom: 15px;
    }
    .profile-preview h2 {
      font-size: 1.8rem;
      color: #333;
      margin-bottom: 5px;
    }
    .profile-preview p {
      color: #666;
      font-size: 0.9rem;
    }
    .settings-options .option {
      margin-bottom: 25px;
    }
    .settings-options .option label {
      display: block;
      font-size: 1rem;
      color: #333;
      margin-bottom: 10px;
    }
    .settings-options .field {
      margin-bottom: 15px;
    }
    .settings-options .field input[type="text"] {
      width: 100%;
      height: 45px;
      padding: 0 15px;
      border: 1px solid #ddd;
      border-radius: 8px;
      margin-bottom: 10px;
      font-size: 15px;
    }
    .settings-options .field input[type="file"] {
      width: 100%;
      padding: 8px;
      border: 1px dashed #ddd;
      border-radius: 8px;
      background: #f8f9fa;
    }
    .settings-options .button input {
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
    .settings-options .button input:hover {
      background: #4a57a5;
    }
    .alert {
      padding: 15px;
      border-radius: 8px;
      margin-bottom: 20px;
      font-size: 14px;
    }
    .alert.error {
      background-color: #fde8e8;
      color: #9b1c1c;
      border: 1px solid #fbd5d5;
    }
    .alert.success {
      background-color: #def7ec;
      color: #03543f;
      border: 1px solid #bcf0da;
    }
    .danger-zone {
      margin-top: 30px;
      padding-top: 20px;
      border-top: 1px solid #ddd;
    }
    .danger-zone h3 {
      color: #dc2626;
      font-size: 1.2rem;
      margin-bottom: 15px;
    }
    .delete-account p {
      color: #666;
      font-size: 0.9rem;
      margin-bottom: 15px;
    }
    .delete-btn {
      width: 100%;
      padding: 12px;
      background-color: #dc2626;
      color: white;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-size: 1rem;
      transition: background-color 0.3s;
    }
    .delete-btn:hover {
      background-color: #b91c1c;
    }
    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0,0,0,0.5);
    }
    .modal-content {
      background-color: #fff;
      margin: 15% auto;
      padding: 30px;
      border-radius: 16px;
      width: 90%;
      max-width: 400px;
      text-align: center;
    }
    .modal-content h2 {
      color: #dc2626;
      margin-bottom: 15px;
    }
    .modal-content p {
      margin-bottom: 25px;
      color: #4b5563;
    }
    .modal-buttons {
      display: flex;
      gap: 10px;
      justify-content: center;
    }
    .confirm-delete, .cancel-delete {
      padding: 10px 20px;
      border-radius: 8px;
      border: none;
      cursor: pointer;
      font-size: 0.9rem;
      transition: background-color 0.3s;
    }
    .confirm-delete {
      background-color: #dc2626;
      color: white;
    }
    .confirm-delete:hover {
      background-color: #b91c1c;
    }
    .cancel-delete {
      background-color: #e5e7eb;
      color: #4b5563;
    }
    .cancel-delete:hover {
      background-color: #d1d5db;
    }
  </style>

  <script>
    const deleteAccountBtn = document.getElementById('deleteAccountBtn');
    const deleteConfirmModal = document.getElementById('deleteConfirmModal');
    const confirmDelete = document.getElementById('confirmDelete');
    const cancelDelete = document.getElementById('cancelDelete');

    deleteAccountBtn.addEventListener('click', () => {
      deleteConfirmModal.style.display = 'block';
    });

    cancelDelete.addEventListener('click', () => {
      deleteConfirmModal.style.display = 'none';
    });

    window.addEventListener('click', (e) => {
      if (e.target === deleteConfirmModal) {
        deleteConfirmModal.style.display = 'none';
      }
    });

    confirmDelete.addEventListener('click', async () => {
      try {
        const response = await fetch('delete_account.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({ action: 'delete_account' })
        });

        const data = await response.json();
        
        if (data.status === 'success') {
          window.location.href = 'logout.php';
        } else {
          alert('Failed to delete account: ' + data.message);
        }
      } catch (error) {
        alert('An error occurred while deleting your account. Please try again.');
      }
    });
  </script>
</body>
</html> 