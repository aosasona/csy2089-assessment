<?php
require_once __DIR__ . "/../src/prelude.php";

use Trulyao\Eds\Auth;

if (!Auth::isLoggedIn()) {
  redirect("/auth.php");
}

render_header("Dashboard");
?>

<main class="container">
</main>

<?php
render_footer();
