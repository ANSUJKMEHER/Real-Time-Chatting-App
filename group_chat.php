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

  // Check if user is a member of this group
  $group_id = mysqli_real_escape_string($conn, $_GET['group_id']);
  $user_id = $_SESSION['unique_id'];
  
  $member_check = mysqli_query($conn, "SELECT * FROM group_members 
                                     WHERE group_id = {$group_id} 
                                     AND unique_id = {$user_id}");
  if(mysqli_num_rows($member_check) == 0) {
    header("location: users.php");
    exit;
  }

  // Update user's status to Active now
  mysqli_query($conn, "UPDATE users SET status = 'Active now' WHERE unique_id = {$user_id}");
?>
<?php include_once "header.php"; ?>
<body>
  <div class="wrapper">
    <section class="chat-area">
      <header>
        <?php 
          $sql = mysqli_query($conn, "SELECT g.*, gm.is_admin 
                                    FROM groups g 
                                    INNER JOIN group_members gm ON g.group_id = gm.group_id 
                                    WHERE g.group_id = {$group_id} AND gm.unique_id = {$user_id}");
          if(mysqli_num_rows($sql) > 0){
            $group = mysqli_fetch_assoc($sql);
          } else {
            header("location: users.php");
            exit;
          }
        ?>
        <a href="users.php" class="back-icon"><i class="fas fa-arrow-left"></i></a>
        <img src="php/images/<?php echo $group['group_image']; ?>" alt="">
        <div class="details">
          <span><?php echo $group['name']; ?></span>
          <p><?php echo $group['is_admin'] ? 'Admin' : 'Member'; ?></p>
        </div>
        <?php if($group['is_admin']): ?>
        <div class="group-actions">
          <a href="manage_group.php?group_id=<?php echo $group_id; ?>" class="manage-group">
            <i class="fas fa-cog"></i>
          </a>
        </div>
        <?php endif; ?>
      </header>
      <div class="chat-box">
      </div>
      <form action="#" class="typing-area" autocomplete="off">
        <input type="hidden" name="group_id" value="<?php echo $group_id; ?>">
        <button type="button" class="emoji-button"><i class="far fa-smile"></i></button>
        <input type="text" name="message" class="input-field" placeholder="Type a message here..." autocomplete="off">
        <button type="button" class="send-btn"><i class="fab fa-telegram-plane"></i></button>
      </form>
    </section>

    <section class="group-info">
      <div class="group-header">
        <img src="php/images/<?php echo $group['group_image']; ?>" alt="">
        <h2><?php echo $group['name']; ?></h2>
      </div>
      
      <div class="members-section">
        <div class="members-header">
          <h3>Members</h3>
          <?php if($group['is_admin']): ?>
          <button class="add-member-btn" onclick="showAddMemberModal()">
            <i class="fas fa-user-plus"></i>
          </button>
          <?php endif; ?>
        </div>
        <div class="members-list">
          <?php
            $members_sql = mysqli_query($conn, "SELECT u.*, gm.is_admin 
                                              FROM users u 
                                              INNER JOIN group_members gm ON u.unique_id = gm.unique_id 
                                              WHERE gm.group_id = {$group_id}
                                              ORDER BY gm.is_admin DESC, u.fname ASC");
            while($member = mysqli_fetch_assoc($members_sql)){
              $status_class = ($member['status'] == "Active now") ? "online" : "offline";
          ?>
          <div class="member">
            <div class="member-info">
              <img src="php/images/<?php echo $member['img']; ?>" alt="">
              <div class="member-details">
                <span><?php echo $member['fname'] . " " . $member['lname']; ?></span>
                <p class="<?php echo $status_class; ?>"><?php echo $member['is_admin'] ? 'Admin' : 'Member'; ?></p>
              </div>
              <?php if($group['is_admin'] && $member['unique_id'] != $_SESSION['unique_id']): ?>
              <button class="remove-member-btn" onclick="showRemoveMemberModal(<?php echo $member['unique_id']; ?>, '<?php echo $member['fname'] . " " . $member['lname']; ?>')">
                <i class="fas fa-user-minus"></i>
              </button>
              <?php endif; ?>
            </div>
          </div>
          <?php } ?>
        </div>
      </div>
    </section>

    <!-- Add Member Modal -->
    <div id="addMemberModal" class="modal">
      <div class="modal-content">
        <span class="close" onclick="closeAddMemberModal()">&times;</span>
        <h2>Add Member</h2>
        <div class="search-box">
          <input type="text" id="searchMember" placeholder="Search users..." onkeyup="searchUsers(this.value)">
        </div>
        <div class="search-results">
          <!-- Search results will be populated here -->
        </div>
      </div>
    </div>

    <!-- Remove Member Modal -->
    <div id="removeMemberModal" class="modal">
      <div class="modal-content">
        <h2>Remove Member</h2>
        <p>Are you sure you want to remove <span id="memberName"></span> from the group?</p>
        <div class="modal-buttons">
          <button id="confirmRemove" class="confirm-remove">Yes, Remove Member</button>
          <button id="cancelRemove" class="cancel-remove">Cancel</button>
        </div>
      </div>
    </div>
  </div>

  <style>
    .wrapper {
      display: flex;
      justify-content: center;
      gap: 20px;
      min-height: 100vh;
      padding: 20px;
    }

    .chat-area {
      flex: 1;
      max-width: 800px;
      position: relative;
    }

    .chat-area::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: url('data:image/svg+xml,<svg width="100" height="100" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg"><path fill="%23e6e6e6" d="M20,20 h30 a10,10 0 0 1 10,10 v20 a10,10 0 0 1 -10,10 h-20 l-10,10 v-10 a10,10 0 0 1 -10,-10 v-20 a10,10 0 0 1 10,-10 z M60,40 h20 a10,10 0 0 1 10,10 v20 a10,10 0 0 1 -10,10 h-20 l-10,10 v-10 a10,10 0 0 1 -10,-10 v-20 a10,10 0 0 1 10,-10 h10 z"/></svg>');
      opacity: 0.1;
      z-index: 0;
      pointer-events: none;
    }

    .chat-box {
      position: relative;
      z-index: 1;
    }

    .group-info {
      width: 300px;
      background: #fff;
      border-radius: 16px;
      padding: 20px;
      box-shadow: 0 0 128px 0 rgba(0,0,0,0.1),
                  0 32px 64px -48px rgba(0,0,0,0.5);
    }

    .group-header {
      text-align: center;
      margin-bottom: 20px;
      padding-bottom: 20px;
      border-bottom: 1px solid #e6e6e6;
    }

    .group-header img {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      margin-bottom: 10px;
    }

    .group-header h2 {
      font-size: 18px;
      color: #333;
    }

    .members-section {
      margin-top: 20px;
    }

    .members-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 15px;
    }

    .members-header h3 {
      font-size: 16px;
      color: #333;
    }

    .add-member-btn {
      background: #5B69C2;
      color: #fff;
      border: none;
      padding: 8px;
      border-radius: 50%;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    .add-member-btn:hover {
      background: #4a57a5;
    }

    .members-list {
      max-height: 400px;
      overflow-y: auto;
    }

    .member {
      padding: 10px 0;
      border-bottom: 1px solid #f0f0f0;
    }

    .member:last-child {
      border-bottom: none;
    }

    .member-info {
      display: flex;
      align-items: center;
    }

    .member-info img {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      margin-right: 10px;
    }

    .member-details span {
      font-size: 14px;
      color: #333;
    }

    .member-details p {
      font-size: 12px;
      color: #67676a;
    }

    .online {
      color: #468669 !important;
    }

    .offline {
      color: #ccc !important;
    }

    /* Modal Styles */
    .modal {
      display: none;
      position: fixed;
      z-index: 1;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0,0,0,0.4);
    }

    .modal-content {
      background-color: #fff;
      margin: 10% auto;
      padding: 20px;
      border-radius: 16px;
      width: 400px;
      position: relative;
    }

    .close {
      position: absolute;
      right: 20px;
      top: 15px;
      font-size: 24px;
      cursor: pointer;
      color: #aaa;
    }

    .close:hover {
      color: #333;
    }

    .search-box {
      margin: 20px 0;
    }

    .search-box input {
      width: 100%;
      padding: 10px;
      border: 1px solid #ddd;
      border-radius: 8px;
      font-size: 14px;
    }

    .search-results {
      max-height: 300px;
      overflow-y: auto;
    }

    .typing-area {
      padding: 18px 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 10px;
      position: relative;
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

    .remove-member-btn {
      background: #dc2626;
      color: white;
      border: none;
      padding: 8px;
      border-radius: 50%;
      cursor: pointer;
      margin-left: 10px;
      transition: background 0.3s ease;
    }

    .remove-member-btn:hover {
      background: #b91c1c;
    }

    .confirm-remove {
      background-color: #dc2626;
      color: white;
    }

    .confirm-remove:hover {
      background-color: #b91c1c;
    }
  </style>

  <script>
    const groupId = <?php echo $group_id; ?>;
    const membersList = document.querySelector('.members-list');

    // Function to update members list with status
    function updateMembersList() {
      fetch(`php/get_group_members.php?group_id=${groupId}`)
        .then(response => response.text())
        .then(data => {
          membersList.innerHTML = data;
        });
    }

    // Update user's active status
    function updateUserStatus() {
      fetch('php/update_status.php');
    }

    // Initial members list load
    updateMembersList();

    // Update members list every 5 seconds
    setInterval(updateMembersList, 5000);

    // Update user's status every 10 seconds
    setInterval(updateUserStatus, 10000);

    function showAddMemberModal() {
      document.getElementById('addMemberModal').style.display = 'block';
    }

    function closeAddMemberModal() {
      document.getElementById('addMemberModal').style.display = 'none';
    }

    function searchUsers(searchTerm) {
      if(searchTerm.length === 0) {
        document.querySelector('.search-results').innerHTML = '';
        return;
      }

      fetch(`php/search_users.php?term=${searchTerm}&group_id=${groupId}`)
        .then(response => response.json())
        .then(data => {
          let html = '';
          if(data.length > 0) {
            data.forEach(user => {
              html += `
                <div class="member">
                  <div class="member-info">
                    <img src="php/images/${user.img}" alt="">
                    <div class="member-details">
                      <span>${user.fname} ${user.lname}</span>
                      <button onclick="addMember(${user.unique_id})" class="add-btn">Add</button>
                    </div>
                  </div>
                </div>`;
            });
          } else {
            html = '<p class="no-results">No users found</p>';
          }
          document.querySelector('.search-results').innerHTML = html;
        });
    }

    function addMember(userId) {
      fetch('php/add_group_member.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `group_id=${groupId}&user_id=${userId}`
      })
      .then(response => response.json())
      .then(data => {
        if(data.status === 'success') {
          location.reload();
        } else {
          alert(data.message);
        }
      });
    }

    let memberToRemove = null;

    function showRemoveMemberModal(memberId, memberName) {
      memberToRemove = memberId;
      document.getElementById('memberName').textContent = memberName;
      document.getElementById('removeMemberModal').style.display = 'block';
    }

    function closeRemoveMemberModal() {
      document.getElementById('removeMemberModal').style.display = 'none';
      memberToRemove = null;
    }

    document.getElementById('confirmRemove').addEventListener('click', async () => {
      if (!memberToRemove) return;

      try {
        const response = await fetch('php/remove_group_member.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: `group_id=${groupId}&member_id=${memberToRemove}`
        });

        const data = await response.json();
        
        if (data.status === 'success') {
          location.reload();
        } else {
          alert('Failed to remove member: ' + data.message);
        }
      } catch (error) {
        alert('An error occurred while removing the member. Please try again.');
      }
    });

    document.getElementById('cancelRemove').addEventListener('click', closeRemoveMemberModal);

    // Close modal when clicking outside
    window.onclick = function(event) {
      if (event.target == document.getElementById('removeMemberModal')) {
        closeRemoveMemberModal();
      }
      if (event.target == document.getElementById('addMemberModal')) {
        closeAddMemberModal();
      }
    }
  </script>
  <script src="javascript/group-emoji-picker.js"></script>
  <script src="javascript/group_chat.js"></script>
</body>
</html> 