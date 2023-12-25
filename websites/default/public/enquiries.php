<?php

use Trulyao\Eds\Auth;
use Trulyao\Eds\Models\Enquiry;


require_once __DIR__ . "/../src/prelude.php";

define("PAGE_SIZE", 50);

if (!Auth::isLoggedIn()) {
  header("Location: /auth.php");
  exit();
}

$user = Auth::getUser();

$page = $_GET["page"] ?? 1;
$total_count = Enquiry::count("asked_by = ?", [$user->id]);
$enquiries = Enquiry::paginateWithQuery(
  "SELECT 
  e.*, p.`name` AS product_name, p.`public_id` AS product_id 
  FROM `enquiries` e 
  LEFT JOIN `products` p ON e.`product_id` = p.id 
  WHERE e.`asked_by` = ? 
  ORDER BY e.`created_at` DESC",
  $page,
  PAGE_SIZE,
  false,
  [$user->id]
);

render_header("Enquiries");
?>

<section></section>
<main>
  <h1>Enquiries</h1>

  <?php if (count($enquiries) === 0) : ?>
    <div class="no-items">
      <p>
        <?php echo ($page === 1) ? "You have not asked any questions yet." : "There are no more questions to show." ?>
    </div>
  <?php endif; ?>

  <ul class="reviews">
    <?php foreach ($enquiries as $enquiry) : ?>
      <li>
        <a href="/product/<?php echo $enquiry["product_id"] ?>">
          <?php echo $enquiry["product_name"] ?>
        </a>
        <p>Q: <?php echo $enquiry["question"] ?></p>
        <p>A: <?php echo $enquiry["answer"] ?: "No response yet" ?></p>

        <div class="details">
          <em>
            <?php echo date("jS F, Y", strtotime($enquiry["created_at"])) ?>
          </em>
        </div>
      </li>
    <?php endforeach; ?>
  </ul>

  <?php render_pagination(current_page: $page, total_count: $total_count, current_count: count($enquiries)); ?>
</main>

<?php render_footer() ?>
