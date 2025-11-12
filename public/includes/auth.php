<?php
function auth_require_login(): void {
  if (empty($_SESSION['uid'])) {
    header('Location: /login.php'); exit;
  }
}
auth_require_login();