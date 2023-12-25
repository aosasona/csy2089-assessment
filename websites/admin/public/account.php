<?php

require_once __DIR__ . "/../src/prelude.php";

use Trulyao\Eds\ClientException;
use Trulyao\Eds\Auth;


require_auth();

if (isset($_POST["change-password"])) {
  try {
    $user = Auth::getUser();
    if (!$user) {
      throw new ClientException("User not found");
    }

    $current_password = $_POST["current_password"] ?? "";
    $new_password = $_POST["new_password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";

    if (!$user->verifyPassword($current_password)) {
      throw new ClientException("Current password is incorrect");
    }

    if (strlen($new_password) < 6) {
      throw new ClientException("New password must be at least 6 characters");
    }

    if ($new_password !== $confirm_password) {
      throw new ClientException("New password and confirm password do not match");
    }

    $user->setPassword($new_password);
    $user->save();

    redirect("/account.php");
  } catch (Exception $e) {
    handle_throwable($e, "/account.php");
  }
}

render_header("Account");
?>

<h2>Manage account</h2>

<div class="mt-4">
  <?php render_error() ?>
</div>

<form method="post" class="md:w-1/2 lg:w-1/3">
  <div class="form-control">
    <label for="current_password">Current Password</label>
    <input type="password" autocomplete="current-password" name="current_password" id="current_password" class="w-full" required />
  </div>

  <div class="form-control">
    <label for="new_password">New Password</label>
    <input type="password" autocomplete="new-password" name="new_password" id="new_password" class="w-full" minlength="6" maxlength="24" required />
  </div>

  <div class="form-control">
    <label for="confirm_password">Confirm Password</label>
    <input type="password" name="confirm_password" id="confirm_password" class="w-full" minlength="6" maxlength="24" required />
  </div>

  <button name="change-password" type="submit" class="mt-4">Change password</button>
</form>

<?php
render_footer();
