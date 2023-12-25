<?php

require_once __DIR__ . "/../src/prelude.php";

use Trulyao\Eds\Models\User;
use Trulyao\Eds\Auth;


if (isset($_GET["logout"])) {
  Auth::logout();
  redirect("/");
} else if (isset($_POST["login"])) {
  try {
    $user = User::findOneBy("email", $_POST["email"]);
    Auth::login($user);
    redirect("/");
  } catch (\Throwable $e) {
    handle_throwable($e, $_SERVER["REQUEST_URI"]);
  }
}

if (Auth::isLoggedIn()) {
  redirect("/");
}

render_header("Authentication");
?>

<section></section>
<main>
  <form method="post">
    <h2>Sign In</h2>

    <?php render_error(); ?>

    <div class="form-control">
      <label for="email">Email</label>
      <input type="email" name="email" id="email" required />
    </div>

    <div class="form-control">
      <label for="password">Password</label>
      <input type="password" name="password" id="password" required />
    </div>

    <div>
      <button type="submit" name="login">Sign In</button>

      <p>
        Don't have an account? <a href="/register.php">Register</a>
      </p>
    </div>
  </form>
</main>

<?php render_footer() ?>
