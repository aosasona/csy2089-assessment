<?php

use Trulyao\Eds\Utils;


use Rakit\Validation\Validator;
use Trulyao\Eds\Auth;
use Trulyao\Eds\ClientException;
use Trulyao\Eds\Models\Category;
use Trulyao\Eds\Models\Permission;
use Trulyao\Eds\Models\Product;

require_once __DIR__ . "/../../src/prelude.php";

require_auth();

$product_id = $_GET["id"] ?? "";
$action = "add-product";
if (!empty($product_id)) {
  try {
    $action = "update-product";
    $current_product = Product::findByPK($product_id);
    if (!$current_product) {
      throw new ClientException("Product not found");
    }
  } catch (Exception $e) {
    handle_throwable($e);
    js_redirect("/", get_error_message());
  }
}


if (isset($_POST["add-product"])) {
  try {
    ensure_user_can(Permission::Write);

    $validator = new Validator();
    $validation = $validator->validate(
      $_POST + $_FILES,
      [
        "name" => "required|min:3|max:96",
        "price" => "required|numeric",
        "category_id" => "required|numeric",
        "description" => "min:3|max:8192",
        "image" => "uploaded_file:0,500K,png,jpeg,jpg",
      ]
    );
    $validation->setAliases(
      [
        "category_id" => "Category ID",
        "is_listed" => "Show listing",
        "is_featured" => "Feature item",
      ]
    );

    if ($validation->fails()) {
      $errors = $validation->errors();
      $error_messages = $errors->firstOfAll();
      throw new ClientException(implode("<br>", $error_messages));
    }

    $file = $_FILES["image"];
    $filename = substr(uniqid(prefix: "prod_", more_entropy: true), 0, 18) . "." . pathinfo($file["name"], PATHINFO_EXTENSION);
    $file_path = "../assets/images/" . $filename;

    if (!file_exists(dirname($file_path))) {
      mkdir(dirname($file_path), 0777, true);
    }

    if (!move_uploaded_file($file["tmp_name"], $file_path)) {
      throw new ClientException("Failed to upload image");
    }

    $is_listed = isset($_POST["is_listed"]) && $_POST["is_listed"] === "on";
    $is_featured = isset($_POST["is_featured"]) && $_POST["is_featured"] === "on";
    $price_as_penies = intval($_POST["price"] * 100);

    $product = new Product();
    $product->name = htmlspecialchars($_POST["name"]);
    $product->public_id = Utils::slugify($product->name) . "-" . substr(time(), 0, 6);
    $product->description = htmlspecialchars($_POST["description"]);
    $product->price = $price_as_penies;
    $product->image_name = $filename;
    $product->category_id = intval($_POST["category_id"]);
    $product->is_listed = $is_listed;
    $product->is_featured = $is_featured;
    $product->listed_by = Auth::getUser()->id;
    $product->save();

    redirect("/");
  } catch (Exception $e) {
    handle_throwable($e, "/manage/product.php");
  }
} elseif (isset($_POST["update-product"])) {
  try {
    ensure_user_can(Permission::Write);
  } catch (Exception $e) {
    handle_throwable($e, "/manage/product.php?id=" . $product_id);
  }
} elseif (isset($_GET["action"]) && $_GET["action"] === "delete") {
  try {
    ensure_user_can(Permission::Delete);
    if (empty($product_id)) {
      throw new ClientException("Malformed request, product ID is missing");
    }
  } catch (Exception $e) {
    handle_throwable($e);
    js_redirect("/", get_error_message());
  }
}


$categories = Category::all();

render_header("New product");
?>

<h2>New product</h2>

<div class="mt-4">
  <?php render_error() ?>
</div>

<form method="POST" class="md:dual-pane mt-4" enctype="multipart/form-data">

  <div>
    <div class="form-control">
      <label for="name">Name</label>
      <input type="text" name="name" id="name" placeholder="20W Anker charger" class="w-full" minlength="3" maxlength="96" />
    </div>

    <div class="md:dual-pane">
      <div class="form-control">
        <label for="price">Price</label>
        <input type="number" inputmode="numeric" name="price" id="price" placeholder="2.99" class="w-full" min="0" max="999999" step="0.01" required />
      </div>

      <div class="form-control">
        <label for="category">Category</label>
        <select name="category_id" id="category" class="w-full" required>
          <option value="">-- Select a category --</option>
          <?php foreach ($categories as $category) : ?>
            <option value="<?php echo $category->id ?>"><?php echo $category->name ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>

    <div class="form-control">
      <label for="image">Product image</label>
      <input type="file" name="image" id="image" class="w-full" accept="image/png,image/jpeg" />
    </div>

    <div class="md:dual-pane mt-4">
      <div class="form-control has-toggle">
        <div class="toggle">
          <input type="checkbox" name="is_listed" id="is_listed" checked="checked" />
          <label for="is_listed"></label>
        </div>
        <p>Show listing</p>
      </div>

      <div class="form-control has-toggle">
        <div class="toggle">
          <input type="checkbox" name="is_featured" id="is_featured" />
          <label for="is_featured"></label>
        </div>
        <p>Feature item</p>
      </div>
    </div>
  </div>

  <div>

    <div class="form-control">
      <label for="description">Description</label>
      <textarea name="description" id="description" rows="12" resize="vertical" class="w-full"></textarea>
    </div>


    <div class="w-full flex justify-end mt-4">
      <button name="<?php echo $action ?>" type="submit" class="w-full sm:w-1/3">Save</button>
    </div>
  </div>
</form>

<?php
render_footer();
