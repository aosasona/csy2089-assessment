<?php

require_once __DIR__ . "/../../src/prelude.php";

use Rakit\Validation\Validation;
use Rakit\Validation\Validator;
use Trulyao\Eds\Utils;
use Trulyao\Eds\Auth;
use Trulyao\Eds\ClientException;
use Trulyao\Eds\Models\Category;
use Trulyao\Eds\Models\Permission;
use Trulyao\Eds\Models\Product;


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


function make_validator(): Validation
{
  $validator = new Validator();
  $validation = $validator->validate(
    $_POST + $_FILES,
    [
      "name" => "required|min:3|max:96",
      "price" => "required|numeric",
      "category_id" => "required|numeric",
      "description" => "min:3|max:8192",
      "image" => "uploaded_file:0,750K,png,jpeg,jpg",
      "manufacturer" => "min:2|max:96",
    ]
  );
  $validation->setAliases(
    [
      "category_id" => "Category ID",
      "is_listed" => "Show listing",
      "is_featured" => "Feature item",
    ]
  );

  return $validation;
}

if (isset($_POST["add-product"])) {
  try {
    ensure_user_can(Permission::Write);

    $validation = make_validator();

    // Throw the first error message
    if ($validation->fails()) {
      throw new ClientException($validation->errors()->firstOfAll()[0]);
    }

    $file = $_FILES["image"];
    $filename = substr(uniqid(prefix: "prod_", more_entropy: true), 0, 18) . "." . pathinfo($file["name"], PATHINFO_EXTENSION);
    $file_path = get_full_product_image_path($filename);

    if (!file_exists(dirname($file_path))) {
      mkdir(dirname($file_path), 0777, true);
      $parent_dir = dirname(dirname($file_path));
      chmod($parent_dir, 0777);
      chmod(dirname($file_path), 0777);
    }

    if (!move_uploaded_file($file["tmp_name"], $file_path)) {
      throw new ClientException("Failed to upload image");
    }

    $is_listed = (int) isset($_POST["is_listed"]) && $_POST["is_listed"] === "on";
    $is_featured = (int) isset($_POST["is_featured"]) && $_POST["is_featured"] === "on";

    $product = new Product();
    $product->name = $_POST["name"];
    $product->public_id = Utils::slugify($product->name) . "-" . substr(time(), 0, 6);
    $product->description = $_POST["description"];
    $product->price = intval($_POST["price"] * 100);
    $product->manufacturer = $_POST["manufacturer"];
    $product->image_name = $filename;
    $product->category_id = (int) $_POST["category_id"];
    $product->is_listed = $is_listed;
    $product->is_featured = $is_featured;
    $product->listed_by = Auth::getUser()->id;
    $product->save();

    redirect("/");
  } catch (Exception $e) {
    // Delete the uploaded file if it exists to prevent clutter
    if (isset($file_path) && file_exists($file_path)) {
      unlink($file_path);
    }

    handle_throwable($e, "/manage/product.php");
  }
} elseif (isset($_POST["update-product"])) {
  try {
    ensure_user_can(Permission::Write);

    $validation = make_validator();
    // Throw the first error message
    if ($validation->fails()) {
      throw new ClientException($validation->errors()->firstOfAll()[0]);
    }

    $file = $_FILES["image"];
    // if a new file was uploaded, delete the old one and save the new one else, just assign the old image name
    if (!empty($file["name"]) && !empty($file["tmp_name"])) {
      $filename = substr(uniqid(prefix: "prod_", more_entropy: true), 0, 18) . "." . pathinfo($file["name"], PATHINFO_EXTENSION);
      $file_path = get_full_product_image_path($filename);

      if (!file_exists(dirname($file_path))) {
        mkdir(dirname($file_path), 0777, true);
        $parent_dir = dirname(dirname($file_path));
        if (!str_ends_with(trim($parent_dir, "/"), "images")) {
          $parent_dir = dirname($parent_dir);
          chmod($parent_dir, 0777);
        }
        chmod(dirname($file_path), 0777);
      }

      if (!move_uploaded_file($file["tmp_name"], $file_path)) {
        throw new ClientException("Failed to upload image");
      }

      // delete the old image
      $old_image_path = get_full_product_image_path($current_product->image_name);
      if (file_exists($old_image_path)) {
        unlink($old_image_path);
      }
    } else {
      $filename = $current_product->image_name;
    }

    $product = new Product();
    $product->name = $_POST["name"];
    $product->description = $_POST["description"];
    $product->manufacturer = $_POST["manufacturer"];
    $product->price = intval($_POST["price"] * 100);
    $product->image_name = $filename;
    $product->category_id = intval($_POST["category_id"]);
    $product->is_listed = (int) isset($_POST["is_listed"]) && $_POST["is_listed"] === "on";
    $product->is_featured = (int) isset($_POST["is_featured"]) && $_POST["is_featured"] === "on";
    $product = $current_product->merge($product);
    $product->save();

    redirect("/");
  } catch (Exception $e) {
    handle_throwable($e, "/manage/product.php?id=" . $product_id);
  }
} elseif (isset($_GET["action"]) && $_GET["action"] === "delete") {
  try {
    ensure_user_can(Permission::Delete);
    if (empty($product_id)) {
      throw new ClientException("Malformed request, product ID is missing");
    }

    $current_product->delete();
    redirect("/");
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
      <input type="text" name="name" id="name" placeholder="20W Anker charger" class="w-full" minlength="3" maxlength="96" value="<?php echo $current_product?->name ?? '' ?>" required />
    </div>

    <div class="md:dual-pane">
      <div class="form-control">
        <label for="price">Price</label>
        <input type="number" inputmode="numeric" name="price" id="price" placeholder="2.99" class="w-full" min="0" max="999999" step="0.01" value="<?php echo isset($current_product) ? ($current_product->price / 100) : '' ?>" required />
      </div>

      <div class="form-control">
        <label for="category">Category</label>
        <select name="category_id" id="category" class="w-full" required>
          <option value="">-- Select a category --</option>
          <?php foreach ($categories as $category) : ?>
            <option value="<?php echo $category->id ?>" <?php echo $category->id === ($current_product->category_id ?? null) ? "selected" : ""  ?>><?php echo $category->name ?></option>
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
          <input type="checkbox" name="is_listed" id="is_listed" <?php echo (bool)($current_product->is_listed ?? $action == "add-product") ? "checked" : "" ?> />
          <label for="is_listed"></label>
        </div>
        <p>Show listing</p>
      </div>

      <div class="form-control has-toggle">
        <div class="toggle">
          <input type="checkbox" name="is_featured" id="is_featured" <?php echo (bool)($current_product->is_featured ?? false) ? "checked" : "" ?> />
          <label for="is_featured"></label>
        </div>
        <p>Feature item</p>
      </div>
    </div>
  </div>

  <div>
    <div class="form-control">
      <label for="manufacturer">Manufacturer</label>
      <input type="text" name="manufacturer" id="manufacturer" placeholder="Anker" class="w-full" value="<?php echo $current_product?->manufacturer ?? '' ?>" />
    </div>

    <div class="form-control">
      <label for="description">Description</label>
      <textarea name="description" id="description" rows="12" resize="vertical" class="w-full"><?php echo $current_product?->description ?? '' ?></textarea>
    </div>

    <div class="w-full flex justify-end mt-4">
      <button name="<?php echo $action ?>" type="submit" class="w-full sm:w-1/3">Save</button>
    </div>
  </div>

</form>

<?php
render_footer();
