<?php

require_once __DIR__ . "/../src/prelude.php";

use Trulyao\Eds\Models\Product;
use Trulyao\Eds\Models\Category;

define("PAGE_SIZE", 25);

/**
 * @var string $slug
 **/

// prevent direct access to this page
if (!isset($slug)) {
  header("Location: /");
  exit;
}


$current_page = $_GET["page"] ?? 1;

/**
 * @var Category $category
 */
$category = Category::findOneBy("slug", $slug);
if (empty($category)) {
  http_response_code(404);
  die("Category not found");
}

$total_count = Product::count("category_id = ?", [$category->id]);
$products = Product::paginateWithQuery(
  sql: "SELECT * FROM `products` WHERE `category_id` = :category_id",
  params: ["category_id" => $category->id],
  page: $_GET["page"] ?? 1,
  as_object: true,
  limit: PAGE_SIZE
);

render_header($category->name . " | Ed's Electronics");
?>

<section></section>
<main>
  <h2>Products</h2>
  <p>
    Browsing by category: <strong><?php echo $category->name ?></strong>
  </p>

  <?php if (empty($products)) : ?>
    <div class="no-items">
      <p>There are no products in this category.</p>
    </div>
  <?php else : ?>
    <ul class="products">
      <?php foreach ($products as $product) : ?>
        <li>
          <h3><a href="/product/<?php echo $product->public_id ?>"><?php echo $product->name ?></a></h3>

          <p>
            <?php echo nl2br($product->description) ?>
          </p>

          <div class="price"><?php echo $product->getPriceAsCurrency() ?></div>
        </li>
      <?php endforeach; ?>
    </ul>

    <?php render_pagination(current_page: $current_page, total_count: $total_count, current_count: count($products)); ?>
  <?php endif; ?>
</main>

<?php render_footer(); ?>
