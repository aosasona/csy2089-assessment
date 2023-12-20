<?php

use Trulyao\Eds\Models\Category;

require_once __DIR__ . "/../../src/prelude.php";

$categories = Category::all();

render_header("New product");
?>

<h2>New product</h2>

<form method="POST" class="mt-4 sm:w-1/2 lg:w-1/3">
  <div class="form-control">
    <label for="name">Name</label>
    <input type="text" name="name" id="name" placeholder="20W Anker charger" class="w-full" />
  </div>

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

  <div class="form-control has-toggle">
    <div class="toggle">
      <input type="checkbox" name="is_available" id="is_available" />
      <label for="is_available"></label>
    </div>
    <p>Available</p>
  </div>

  <button type="submit">Save</button>
</form>

<?php
render_footer();
