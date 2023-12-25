<?php

/**
 * @var $show_featured bool
 */

if ($show_featured) {
  $featured_products = \Trulyao\Eds\Models\Product::query("SELECT p.`name`, p.`image_name`, p.`public_id`, c.`name` AS `category` FROM `products` p LEFT JOIN `categories` c ON c.`id` = p.`category_id` WHERE `is_featured` = 1");
  $random_index = array_rand($featured_products); // get a random index from the array so we display a random product every time
  $featured_product = $featured_products[$random_index];
}

?>

<?php if ($show_featured) : ?>
  <aside>
    <h1><a href="/product/<?php echo $featured_product['public_id'] ?>">Featured Product</a></h1>
    <img src="<?php echo get_product_image_url($featured_product['image_name']) ?>" alt="<?php echo $featured_product['name'] ?>" class="product_image full-width" />
    <p><strong><?php echo $featured_product["category"] ?></strong></p>
    <p><?php echo $featured_product["name"] ?></p>
  </aside>
<?php endif; ?>

<footer>&copy; Ed's Electronics 2023</footer>
</body>

</html>
