<?php

function drawMessage($message, $is_client, $user, $freelancer) {
    // $message: array with keys: message_text, is_reply, created_at
    // $is_client: bool, true if the current user is the client
    // $user: array, the client user
    // $freelancer: array, the freelancer user
    $is_reply = (bool)$message['is_reply'];
    $sender = $is_reply ? $freelancer : $user;
    $avatar = !empty($sender['user_picture']) ? '../action/get_profile_picture.php?id=' . $sender['user_id'] : '../assets/images/default.jpg';
    $name = htmlspecialchars($sender['full_name']);
    $date = isset($message['date_time']) ? date('d/m/Y', strtotime($message['date_time'])) : '';
    ?>
    <div class="forum-message">
      <a href="../pages/profile.php?id=<?= urlencode($sender['user_id']) ?>">
        <img src="<?= $avatar ?>" alt="<?= $name ?>" class="forum-avatar" style="cursor:pointer;" />
      </a>
      <div class="forum-message-content">
        <div class="forum-message-header">
          <a style="text-decoration:none;" href="../pages/profile.php?id=<?= urlencode($sender['user_id']) ?>" style="color:inherit;text-decoration:underline;cursor:pointer;">
            <span class="forum-username"><?= $name ?></span>
          </a>
          <span class="forum-date"><?= $date ?></span>
        </div>
        <div class="forum-text"><?= nl2br(htmlspecialchars($message['message_text'])) ?></div>
      </div>
    </div>
    <?php
}
