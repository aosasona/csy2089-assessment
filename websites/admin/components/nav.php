<?php

use Trulyao\Eds\Auth;
use Trulyao\Eds\Models\Permission;


try {
  $user = Auth::getUser();
} catch (Exception $e) {
  http_response_code(500);
  exit;
}


function render_nav_link(string $url, string $text, array $matches = []): void
{
  $matches[] = trim($url, "/"); // Add the current URL to the list of matches
  $current_url = trim(
    str_contains($_SERVER["REQUEST_URI"], "?") ? explode("?", $_SERVER["REQUEST_URI"])[0] : $_SERVER["REQUEST_URI"],
    "/"
  );
  $active = in_array($current_url, $matches) ? "active" : "";
  echo "<a href=\"$url\" class=\"$active\">$text</a>";
}

?>
<nav>
  <div class="nav-bar">
    <p>Signed in as <b><?php echo $user->username ?></b></p>
  </div>

  <div class="nav-links">
    <?php
    render_nav_link("/", "Products", ["manage/product.php"]);
    render_nav_link("/categories.php", "Categories", ["manage/category.php"]);
    render_nav_link("/enquiries.php", "Enquiries");
    if ($user->can(Permission::ManageUsers)) {
      render_nav_link("/manage_users.php", "Manage users");
    }
    render_nav_link("/account.php", "Account");
    ?>
    <a class="logout" data-confirm="Are you sure you want to logout?" href="/auth.php?logout">Logout</a>
  </div>
</nav>
