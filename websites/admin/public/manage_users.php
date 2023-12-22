<?php


use Trulyao\Eds\Models\Permission;
use Trulyao\Eds\Models\User;

require_once __DIR__ . "/../src/prelude.php";

require_auth();
ensure_user_can(Permission::ManageUsers);

$admins = User::where("is_admin = 1", []);

render_header("Manage Users");

?>

<h2>Manage Users</h2>

<?php render_error(); ?>

<?php
render_footer();
?>
