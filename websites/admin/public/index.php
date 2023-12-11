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
    <button title="Create new product">New product</button>
  </div>

  <div>
    <?php if (count($products) > 0) : ?>
      <!-- TODO: add forward and backwards buttons -->
      <p>Displaying <b><?php echo count($products) ?></b> of <b><?php echo $total_products ?></b> products</p>
    <?php endif; ?>
  </div>

  <table class="items-list">
    <thead>
      <th>Image</th>
      <th>Name</th>
      <th>Price</th>
      <th>Category</th>
      <th>Is listed</th>
      <th>Is featured</th>
    </thead>

    <?php if (count($products) == 0) : ?>
      <tr class="no-items">
        <td colspan="6">No products found</td>
      </tr>
    <?php endif; ?>
  </table>
</main>

<?php
render_footer();
