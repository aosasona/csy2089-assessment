<?php

require_once __DIR__ . "/../vendor/autoload.php";

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

function render_header(string $title = "Ed's Electronics"): void
{
  include_once  __DIR__ . "/../components/header.php";
}

function render_footer(bool $show_featured = true): void
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

function get_product_image_url(string $image_name): string
{
  return "/images/products/$image_name";
}

function render_pagination(int $current_count, int $total_count, int $current_page): void
{
  include __DIR__ . "/../../admin/components/pagination.php";
}
