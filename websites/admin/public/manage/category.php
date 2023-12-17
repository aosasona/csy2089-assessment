<?php

use Trulyao\Eds\Models\Condition;

use Trulyao\Eds\Models\Category;

use Trulyao\Eds\ClientException;

require_once __DIR__ . "/../../src/prelude.php";


$category_id = $_GET["id"] ?? "";
$action = (!empty($category_id) ? "update" : "create") . "-category";

if (isset($_POST["create-category"])) {
  try {
    $name = htmlspecialchars($_POST["name"]);
    if (Category::exists(["name" => $name, "slug" => Category::slugify($name)], Condition::OR)) {
      throw new ClientException("Category `{$name}` already exists");
    }

    // create new record
    $category = new Category();
    $category->name = $name;
    $category->save();

    redirect("/categories.php");
  } catch (Exception $e) {
    handle_throwable($e, "/manage/category.php");
  }
} elseif (isset($_POST["update-category"]) && !empty($_POST["category_id"])) {
  try {
    // TODO: handle this
  } catch (Exception $e) {
    handle_throwable($e, "/create/category.php?id=" . $category_id);
  }
}

render_header("New category");
?>

<h2>New category</h2>

<form method="POST" class="mt-4">
  <?php render_error(); ?>

  <input type="hidden" name="category_id" value="<?php echo $category_id ?>" />
  <div class="form-control">
    <label for="name">Name</label>
    <input type="text" name="name" id="name" class="w-1/2" placeholder="Consoles, TVs etc" />
  </div>

  <button name="<?php echo $action;  ?>" type="submit">Save</button>
</form>

<?php
render_footer();
