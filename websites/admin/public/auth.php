<?php


require_once __DIR__ . "/../src/prelude.php";

use Trulyao\Eds\Auth;
use Trulyao\Eds\Models\User;
use Trulyao\Eds\ClientException;


if (isset($_GET['logout'])) {
  Auth::logout();
  redirect("/auth.php");
} elseif (Auth::isLoggedIn()) {
  redirect("/");
}

if (isset($_POST['sign_in'])) {
  try {
    $username = htmlspecialchars($_POST['username']);
    if (empty($username)) {
      throw new ClientException("Username is required");
    }
    $password = htmlspecialchars($_POST['password']);
    if (empty($password)) {
      throw new ClientException("Password is required");
    }

    $user = User::findBy("username", $username);

    if (!$user || !password_verify($password, $user->password)) {
      throw new ClientException("Invalid username or password");
    }

    if (!$user->is_admin) {
      throw new ClientException("You are not authorized to access this page");
    }

    Auth::login($user, isset($_POST['remember_me']));
    redirect("/");
  } catch (ClientException $e) {
    $_SESSION['error'] = $e->getMessage();
    redirect("/auth.php");
  } catch (Exception $e) {
    $_SESSION['error'] = "Something went wrong";
    redirect("/auth.php");
  }
}

$err = $_SESSION['error'] ?? null;
unset($_SESSION['error']);

render_header("Login");
?>

<main class="container flex flex-center h-screen">
  <form id="auth" method="POST">
    <?php if (isset($err)) : ?>
      <div class="error"><?php echo $err ?></div>
    <?php endif; ?>
    <div class="form-control">
      <label for="username">Username</label>
      <input type="text" name="username" id="username" placeholder="jdoe" />
    </div>

    <div class="form-control">
      <label for="password">Password</label>
      <input type="password" name="password" id="password" />
    </div>

    <div class="form-control checkbox">
      <input type="checkbox" name="remember_me" id="remember_me" />
      <label for="remember_me">Remember Me</label>
    </div>

    <button name="sign_in" type="submit">Sign In</button>
  </form>
</main>

<?php
render_footer();
