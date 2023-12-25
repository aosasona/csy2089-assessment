<?php
require_once __DIR__ . "/../src/prelude.php";

use Trulyao\Eds\Models\Product;

define("PAGE_SIZE", 10);
$page = $_GET["page"] ?? 1;
$products = Product::paginate($page, PAGE_SIZE);

render_header();
?>

<section></section>
<main>
  <h1>Welcome to Ed's Electronics</h1>

  <p>
    We stock a large variety of electrical goods including phones, tvs, computers and games. Everything comes with at
    least a one year guarantee and free next day delivery.
  </p>

  <hr />

  <h2>Product list</h2>

  <?php if (empty($products)) : ?>
    <p>There are no products in this category.</p>
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
  <?php endif; ?>

</main>

<?php render_footer(); ?>
