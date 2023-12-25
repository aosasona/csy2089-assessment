<?php


use Trulyao\Eds\ClientException;
use Trulyao\Eds\Models\User;

require_once __DIR__ . "/../src/prelude.php";

if (isset($_POST["register"])) {
  try {
    $firstname = htmlspecialchars($_POST["first_name"]);
    $lastname = htmlspecialchars($_POST["last_name"]);
    $username = htmlspecialchars($_POST["username"]);
    $email = htmlspecialchars($_POST["email"]);
    $password = htmlspecialchars($_POST["password"]);
    $confirm_password = htmlspecialchars($_POST["confirm_password"]);

    if (empty($firstname) || strlen($firstname) < 2) {
      throw new ClientException("First name is required and must be at least 2 characters");
    }

    if (empty($lastname) || strlen($lastname) < 2) {
      throw new ClientException("First name is required and must be at least 2 characters");
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
      throw new ClientException("Email is required and must be a valid email address");
    }

    if (empty($username) || strlen($username) < 4 || !preg_match("/^[a-zA-Z0-9]+$/", $username)) {
      throw new ClientException("Username is required and must be at least 2 characters and contain only letters and numbers");
    }

    if (empty($password) || strlen($password) < 6) {
      throw new ClientException("Password is required and must be at least 6 characters");
    }

    if ($password !== $confirm_password) {
      throw new ClientException("Passwords do not match");
    }

    $user = new User();
    $user->first_name = $firstname;
    $user->last_name = $lastname;
    $user->email = $email;
    $user->username = $username;
    $user->setPassword($password);
    $user->save();

    redirect("/auth.php");
  } catch (\Throwable $e) {
    handle_throwable($e, $_SERVER["REQUEST_URI"]);
  }
}


render_header("Create an account");
?>

<section></section>
<main>
  <form method="post">
    <h2>Register</h2>

    <?php render_error(); ?>

    <div class="dual-pane">
      <div class="form-control">
        <label for="first_name">First Name</label>
        <input type="text" name="first_name" id="first_name" required />
      </div>

      <div class="form-control">
        <label for="last_name">Last Name</label>
        <input type="text" name="last_name" id="last_name" required />
      </div>
    </div>

    <div class="form-control">
      <label for="email">Email</label>
      <input type="email" name="email" id="email" required />
    </div>

    <div class="form-control">
      <label for="username">Username</label>
      <input type="text" name="username" id="username" minlength="4" required />
    </div>

    <div class="dual-pane">
      <div class="form-control">
        <label for="password">Password</label>
        <input type="password" name="password" id="password" minlength="6" required />
      </div>

      <div class="form-control">
        <label for="confirm_password">Confirm Password</label>
        <input type="password" name="confirm_password" id="confirm_password" minlength="6" required />
      </div>
    </div>

    <div>
      <button type="submit" name="register">Register</button>

      <p>
        Already have an account? <a href="/auth.php">Sign In</a>
      </p>
    </div>
  </form>
</main>
<?php render_footer() ?>
