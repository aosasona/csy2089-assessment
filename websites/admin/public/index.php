<?php

require_once __DIR__ . "/../src/prelude.php";

use Trulyao\Eds\Auth;
use Trulyao\Eds\Models\Permission;
use Trulyao\Eds\Models\Product;

require_auth();
ensure_user_can(Permission::Read);

define("PAGE_SIZE", 50);

$current_page = $_GET["page"] ?? 1;

$total_products = Product::count();
$products = Product::paginateWithQuery(
  "SELECT p.*, c.`name` as `category_name`, CONCAT(u.`first_name`, ' ', u.`last_name`) as `listed_by` FROM `products` p INNER JOIN `categories` c ON p.`category_id` = c.id INNER JOIN `users` u ON u.`id` = p.`listed_by` ORDER BY p.`created_at` DESC",
  $current_page,
  PAGE_SIZE
);

$host_url = $_SERVER["REQUEST_SCHEME"] . "://" . str_replace("admin.", "", $_SERVER["HTTP_HOST"]);

render_header("Products");
?>

<main class="mt-4">
  <div class="w-full flex justify-end">
    <a href="/manage/product.php" class="btn" title="Create new product">New product</a>
  </div>



  <?php if (count($products) === 0) : ?>
    <p class="no-items">No products found</p>
  <?php else : ?>
    <div class="list-container">
      <table class="list">
        <thead>
          <tr>
            <th scope="col"></th>
            <th scope="col">Product name</th>
            <th scope="col">Description</th>
            <th scope="col">Category</th>
            <th scope="col">Manufacturer</th>
            <th scope="col">Price</th>
            <th scope="col">Published</th>
            <th scope="col">Featured</th>
            <th scope="col">Listed by</th>
            <th scope="col">Added on</th>
            <th scope="col"></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($products as $product) : ?>
            <tr>
              <td>
                <img src="<?php echo $host_url ?>/images/products/<?php echo $product['image_name'] ?>" alt="<?php echo $product['name'] ?>" />
              </td>
              <th scope="row">
                <?php echo $product['name'] ?>
              </th>
              <td>
                <?php echo (strlen($product['description']) > 100) ? substr($product['description'], 0, 100) . "..." : $product['description'] ?>
              </td>
              <td>
                <?php echo $product['category_name'] ?>
              </td>
              <td>
                <?php echo $product['manufacturer'] ?>
              </td>
              <td>
                £<?php echo number_format($product['price'] / 100, 2) ?>
              </td>
              <td>
                <?php echo $product['is_listed'] ? "Yes" : "No" ?>
              </td>
              <td>
                <?php echo $product['is_featured'] ? "Yes" : "No" ?>
              </td>
              <td>
                <?php echo $product['listed_by'] ?>
              </td>
              <td>
                <?php echo date("d/m/Y H:i:s", strtotime($product['created_at'])) ?>
              </td>
              <td class="flex gap-2">
                <?php if (Auth::getUser()?->can(Permission::Write)) : ?>
                  <a href="/manage/product.php?id=<?php echo $product['id']; ?>" class="action f16">
                    <i class="ti ti-edit"></i>
                  </a>
                <?php endif; ?>

                <?php if (Auth::getUser()?->can(Permission::Delete)) : ?>
                  <a href="/manage/product.php?id=<?php echo $product['id']; ?>&action=delete" class="action f16 delete" data-confirm="Are you sure you want to delete this product? THIS ACTION CANNOT BE REVERSED.">
                    <i class="ti ti-trash"></i>
                  </a>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <?php render_pagination(count($products), $total_products, $current_page); ?>

  <?php endif; ?>
</main>

<?php
render_footer();
