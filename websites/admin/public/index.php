<?php
require_once __DIR__ . "/../src/prelude.php";

use Trulyao\Eds\Auth;
use Trulyao\Eds\Models\Product;

if (!Auth::isLoggedIn()) {
  redirect("/auth.php");
}

$current_page = $_GET["page"] ?? 1;

$total_products = Product::count();
$products = Product::paginate($current_page, 50);

render_header("Products");
?>

<main class="mt-4">
  <div class="w-full flex justify-end">
    <a href="/manage/product.php" class="btn" title="Create new product">New product</a>
  </div>

  <div>
    <?php if (count($products) > 0) : ?>
      <!-- TODO: add forward and backwards buttons -->
      <p>Displaying <b><?php echo count($products) ?></b> of <b><?php echo $total_products ?></b> products</p>
    <?php endif; ?>
  </div>


  <?php if (count($products) === 0) : ?>
    <p class="no-items">No products found</p>
  <?php else : ?>
    <!-- TODO: definitely create custom css classes -->
    <div class="container">
      <table class="list">
        <thead>
          <tr>
            <th scope="col">Product name</th>
            <th scope="col">Color</th>
            <th scope="col">Category</th>
            <th scope="col">Price</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <th scope="row">
              Apple MacBook Pro 17"
            </th>
            <td>
              Silver
            </td>
            <td>
              Laptop
            </td>
            <td>
              $2999
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</main>

<?php
render_footer();
