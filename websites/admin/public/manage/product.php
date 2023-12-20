<?php


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
    $msg = get_error_message();
    echo "<script>alert('{$msg}');location.href='/'</script>";
  }
}


$categories = Category::all();

render_header("New product");
?>

<h2>New product</h2>

<form method="POST" class="md:dual-pane mt-4" enctype="multipart/form-data">
  <div>
    <div class="form-control">
      <label for="name">Name</label>
      <input type="text" name="name" id="name" placeholder="20W Anker charger" class="w-full" />
    </div>

    <div class="md:dual-pane">
      <div class="form-control">
        <label for="price">Price</label>
        <input type="number" inputmode="numeric" name="price" id="price" placeholder="2.99" class="w-full" />
      </div>

      <div class="form-control">
        <label for="category">Category</label>
        <select name="category_id" id="category" class="w-full">
          <option value="">-- Select a category --</option>
          <?php foreach ($categories as $category) : ?>
            <option value="<?php echo $category->id ?>"><?php echo $category->name ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>

    <div class="form-control">
      <label for="image">Product image</label>
      <input type="file" name="image" id="image" class="w-full" />
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
