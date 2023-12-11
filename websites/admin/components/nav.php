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
  $current_url = $_SERVER["REQUEST_URI"];
  $active = in_array($current_url, $matches + [$url]) ? "active" : "";
  echo "<a href=\"$url\" class=\"$active\">$text</a>";
}

?>
<nav>
  <div class="nav-bar">
    <p>Signed in as <b><?php echo $user->username ?></b></p>
  </div>

  <div class="nav-links">
    <?php
    render_nav_link("/", "Products");
    render_nav_link("/categories.php", "Categories");
    render_nav_link("/enquiries.php", "Enquiries");
    if ($user->can(Permission::ManageUsers)) {
      render_nav_link("/manage-users.php", "Manage users");
    }
    render_nav_link("/account.php", "Account");
    ?>
    <a class="logout" href="/auth.php?logout">Logout</a>
  </div>
</nav>
