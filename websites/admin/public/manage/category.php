<?php

use Trulyao\Eds\Models\Permission;

use Trulyao\Eds\Models\Condition;
use Trulyao\Eds\Models\Category;
use Trulyao\Eds\ClientException;

require_once __DIR__ . "/../../src/prelude.php";

require_auth();

$category_id = $_GET["id"] ?? "";
$action = "create-category";
if (!empty($category_id)) {
  try {
    $action = "update-category";
    $current_category = Category::findByPK($category_id);
    if (!$current_category) {
      throw new ClientException("Category not found");
    }
  } catch (Exception $e) {
    handle_throwable($e);
    js_redirect("/categories.php", get_error_message());
  }
}


if (isset($_POST["create-category"])) {
  try {
    ensure_user_can(Permission::Write);

    $name = htmlspecialchars($_POST["name"]);
    if (Category::exists(["name" => $name, "slug" => Category::slugify($name)], Condition::OR)) {
      throw new ClientException("Category `{$name}` already exists");
    }

    $category = new Category();
    $category->name = $name;
    $category->save();

    redirect("/categories.php");
  } catch (Exception $e) {
    handle_throwable($e, "/manage/category.php");
  }
} elseif (isset($_POST["update-category"])) {
  try {
    ensure_user_can(Permission::Write);

    $current_category->name = htmlspecialchars($_POST["name"]);
    $current_category->save();
    redirect("/categories.php");
  } catch (Exception $e) {
    handle_throwable($e, "/manage/category.php?id=" . $category_id);
  }
} elseif (isset($_GET["action"]) && $_GET["action"] === "delete") {
  try {
    ensure_user_can(Permission::Delete);

    if (empty($category_id)) {
      throw new ClientException("Category ID cannot be empty");
    }

    $current_category->delete();
    redirect("/categories.php");
  } catch (Exception $e) {
    handle_throwable($e);
    js_redirect("/categories.php", get_error_message());
  }
}


render_header("New category");
?>

<h2>New category</h2>

<form method="POST" class="mt-4">
  <?php render_error(); ?>

  <div class="form-control">
    <label for="name">Name</label>
    <input type="text" name="name" id="name" class="w-1/2" placeholder="Consoles, TVs etc" value="<?php echo $current_category->name ?? '' ?>" />
  </div>

  <button name="<?php echo $action;  ?>" type="submit">Save</button>
</form>

<?php
render_footer();
