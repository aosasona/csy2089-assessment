<?php

require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../../default/vendor/autoload.php";

use Trulyao\Eds\Auth;

if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}

function redirect(string $url): void
{
  header("Location: $url", true, 303);
  exit;
}

// Utility function to redirect with a message and alert using JavaScript
// This is useful when you want to redirect the user after echoing some HTML content (using header() would fail in this case)
function js_redirect(string $url, string $msg): void
{
  echo "<script>alert('{$msg}'); location.href='{$url}'</script>";
  exit;
}

function require_auth(): void
{
  if (!Auth::isLoggedIn()) {
    redirect("/auth.php");
  }
}

function ensure_user_can(...$permissions): void
{
  if (!Auth::isLoggedIn()) {
    redirect("/auth.php");
  } elseif (!Auth::getUser()->can(...$permissions) || !Auth::getUser()->is_admin) {
    render_header("Unauthorized");
    echo <<<HTML
      <main class="container flex flex-center h-half-screen">
        <p>You are not authorized to access this page or perform this action!</p>
      </main>
    HTML;
    render_footer();
    exit;
  }
}

// Pretty dump
function dd(...$vars): void
{
  foreach ($vars as $var) {
    echo '<pre style="display: block; max-width:700px; background-color: #f4f4f5; border-radius: 10px; border: 1px solid #e4e4e7; margin-bottom: 1rem; margin-inline: auto; padding: .5rem 1rem;">';
    echo "<b>" . $_SERVER["PHP_SELF"] . "</b><br/><br/>";
    print_r($var);
    echo "</pre>";
  }
  exit;
}

function render_header(string $title): void
{
  include_once  __DIR__ . "/../components/header.php";
  if (Auth::isLoggedIn()) {
    include_once  __DIR__ . "/../components/nav.php";
  }
}

function render_footer(): void
{
  include_once  __DIR__ . "/../components/footer.php";
}

// Utility function to handle exceptions and properly filter the error message
function handle_throwable(Throwable $e, ?string $redirect = null): void
{
  if ($e instanceof \Trulyao\Eds\ClientException) {
    $_SESSION["error"] = $e->getMessage();
  } else {
    $_SESSION["error"] = "Something went wrong, please try again later.";
  }

  if (!empty($redirect)) {
    redirect($redirect);
  }
}

// Utility function to get the error message from the session and clear it to prevent it from showing up again after a refresh
function get_error_message(): ?string
{
  $error = $_SESSION["error"] ?? null;
  if (!empty($error)) {
    unset($_SESSION["error"]);
  }
  return $error;
}

function render_error()
{
  if ($error = get_error_message()) {
    echo "<div class=\"error\">$error</div>";
  }
}

function validate_post_regex($name, $regex)
{
  return filter_input(
    INPUT_POST,
    $name,
    FILTER_VALIDATE_REGEXP,
    ["options" => ["regexp" => $regex]]
  );
}

function get_full_product_image_path($image_name)
{
  return "../../../default/public/images/products/" . $image_name;
}
