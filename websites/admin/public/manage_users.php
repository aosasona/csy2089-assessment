<?php


require_once __DIR__ . "/../src/prelude.php";

use Trulyao\Eds\ClientException;
use Trulyao\Eds\Models\Permission;
use Trulyao\Eds\Models\User;

require_auth();

ensure_user_can(Permission::ManageUsers);

define("PAGE_SIZE", 50);

$current_page = $_GET["page"] ?? 1;
$total_admins = User::count("is_admin = 1");
$admins = User::paginateWithQuery("SELECT * FROM users WHERE is_admin = 1", $current_page, PAGE_SIZE, true);

if (isset($_POST["add-user"])) {
  try {
    $first_name = $_POST["first_name"] ?? "";
    $last_name = $_POST["last_name"] ?? "";
    $password = $_POST["password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";
    $permissions = $_POST["permissions"] ?? [];

    if (strlen($first_name) < 2) {
      throw new ClientException("First name must be at least 2 characters");
    }

    if (strlen($last_name) < 2) {
      throw new ClientException("Last name must be at least 2 characters");
    }

    if (strlen($password) < 6) {
      throw new ClientException("Password must be at least 6 characters");
    }

    if ($password !== $confirm_password) {
      throw new ClientException("Password and confirm password do not match");
    }

    $username = substr($first_name, 0, 1) . $last_name;
    $perm = array_reduce($permissions, fn ($acc, $p) => $acc | $p, 0);

    $user = new User();
    $user->first_name = $first_name;
    $user->last_name = $last_name;
    $user->username = $username;
    $user->email = $username . "@v.je";
    $user->password = $password;
    $user->is_admin = true;
    $user->perm = $perm;

    dd($user);
  } catch (Exception $e) {
    $_SESSION["prev"] = $_POST;
    handle_throwable($e, "/manage_users.php");
  }
}


$prev = $_SESSION["prev"] ?? [];
unset($_SESSION["prev"]);

function perm_is_checked(Permission $value)
{
  global $prev;
  echo in_array($value->value, $prev['permissions'] ?? []) ? 'checked' : '';
}

render_header("Manage Users");

?>

<h2>Manage Users</h2>

<div class="md:flex w-full gap-6 mt-4">
  <div class="w-full">
    <?php if (count($admins) === 0) : ?>
      <!-- this will probably NEVER happen but just in case -->
      <p class="no-items">No admins</p>
    <?php else : ?>
      <div class="list-container">
        <table class="list">
          <thead>
            <tr>
              <th scope="col">ID</th>
              <th scope="col">First name</th>
              <th scope="col">Last name</th>
              <th scope="col">Username</th>
              <th scope="col">Email</th>
              <th scope="col">Added on</th>
              <th scope="col"></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($admins as $admin) : ?>
              <tr>
                <th scope="row"><?php echo $admin->id ?></th>
                <td><?php echo $admin->first_name ?></td>
                <td><?php echo $admin->last_name ?></td>
                <td><?php echo $admin->username ?></td>
                <td><?php echo $admin->email ?></td>
                <td><?php echo date("d/m/Y H:i:s", strtotime($admin->created_at)) ?></td>
                <td class="flex gap-2">
                  <a title="Remove admin privileges" href="?id=<?php echo $admin->id; ?>&action=remove-priv" class="action f16" data-confirm="Are you sure you want to remove admin privileges from this user?">
                    <i class="ti ti-arrow-back-up"></i>
                  </a>
                  <a title="Delete user" href="?id=<?php echo $admin->id; ?>&action=delete" class="action f16 delete" data-confirm="Are you sure you want to DELETE this user? THIS ACTION CANNOT BE REVERSED, it will remove the user entirely and not just their permissions">
                    <i class="ti ti-trash"></i>
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>

    <?php render_pagination(count($admins), $total_admins, $current_page); ?>
  </div>

  <form class="w-full md:w-1/2 lg:w-1/3" method="POST">
    <?php render_error(); ?>
    <div class="form-control">
      <label for="first_name">First name</label>
      <input type="text" id="first_name" name="first_name" placeholder="john" class="w-full" value="<?php echo $prev['first_name'] ?? '' ?>" />
    </div>

    <div class="form-control">
      <label for="last_name">Last name</label>
      <input type="text" id="last_name" name="last_name" placeholder="doe" class="w-full" value="<?php echo $prev['last_name'] ?? '' ?>" />
    </div>

    <div class="form-control">
      <label for="password">Password</label>
      <input type="password" autocomplete="new-password" id="password" name="password" placeholder="******" minlength="6" maxlength="24" class="w-full" />
    </div>

    <div class="form-control">
      <label for="confirm_password">Confirm Password</label>
      <input type="password" id="confirm_password" name="confirm_password" placeholder="******" minlength="6" maxlength="24" class="w-full" />
    </div>

    <div class="form-control">
      <p><b>Permissions</b> <sup><a href="#" id="toggle-select-all-permissions">(select all)</a></sup></p>

      <div class="form-control has-toggle mt-4">
        <div class="toggle">
          <input type="checkbox" name="permissions[]" id="read" value="<?php echo Permission::Read->value ?>" <?php perm_is_checked(Permission::Read) ?> />
          <label for="read"></label>
        </div>
        <p>Read (all except users)</p>
      </div>

      <div class="form-control has-toggle">
        <div class="toggle">
          <input type="checkbox" name="permissions[]" id="write" value="<?php echo Permission::Write->value ?>" <?php perm_is_checked(Permission::Write) ?> />
          <label for="write"></label>
        </div>
        <p>Write (all except users)</p>
      </div>

      <div class="form-control has-toggle">
        <div class="toggle">
          <input type="checkbox" name="permissions[]" id="delete" value="<?php echo Permission::Delete->value ?>" <?php perm_is_checked(Permission::Delete) ?> />
          <label for="delete"></label>
        </div>
        <p>Delete (all except users)</p>
      </div>

      <div class="form-control has-toggle">
        <div class="toggle">
          <input type="checkbox" name="permissions[]" id="manage_users" value="<?php echo Permission::ManageUsers->value ?>" <?php perm_is_checked(Permission::ManageUsers) ?> />
          <label for="manage_users"></label>
        </div>
        <p>Manage users (read, write &amp; delete)</p>
      </div>
    </div>

    <div class="flex justify-end">
      <button name="add-user" type="submit">Add User</button>
    </div>
  </form>
</div>

<?php
render_footer();
?>
