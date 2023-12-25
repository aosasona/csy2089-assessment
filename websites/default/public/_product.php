<?php


use Trulyao\Eds\Auth;
use Trulyao\Eds\Models\Enquiry;
use Trulyao\Eds\Models\Product;

require_once __DIR__ . "/../src/prelude.php";

/**
 * @var string $public_id
 **/

// prevent direct access to this page
if (!isset($public_id)) {
  header("Location: /");
  exit;
}


$product = Product::findOneBy("public_id", $public_id);
if (empty($product)) {
  http_response_code(404);
  die("Product not found");
}

if (isset($_POST["add-enquiry"])) {
  try {
    $user_id = Auth::isLoggedIn() ? Auth::getUser()->id : null;
    Enquiry::ask(
      question: htmlspecialchars($_POST["question"]),
      product_id: $product->id,
      asked_by: $user_id,
      defer_publishing: true
    );

    redirect($_SERVER["REQUEST_URI"]);
  } catch (\Throwable $e) {
    handle_throwable($e, $_SERVER["REQUEST_URI"]);
  }
}

$enquiries = Enquiry::forProduct($product->id);

render_header($product->name . " | Ed's Electronics");
?>

<section></section>
<main>
  <img src="<?php echo get_product_image_url($product->image_name) ?>" alt="<?php echo $product->name ?>" class="product_image preview" />
  <h2><?php echo $product->name ?></h2>

  <h4>Product details</h4>
  <p><?php echo nl2br($product->description); ?></p>

  <hr />

  <div>
    <h2>Question</h2>

    <?php render_error(); ?>

    <form method="post">
      <p>Type your question/review here:</p>
      <textarea rows="14" name="question"></textarea>

      <div class="btn-container">
        <button type="submit" name="add-enquiry">Submit</button>
      </div>
    </form>
  </div>

  <hr />

  <h4>Product reviews</h4>
  <?php if (empty($enquiries)) : ?>
    <div class="no-items">
      <p>There are no reviews for this product.</p>
    </div>
  <?php else : ?>
    <ul class="reviews">
      <?php foreach ($enquiries as $enquiry) : ?>
        <li>
          <p>Q: <?php echo $enquiry["question"] ?></p>
          <p>A: <?php echo $enquiry["answer"] ?: "No response yet" ?></p>

          <div class="details">
            <strong>
              <?php
              $name = $enquiry["first_name"] . " " . $enquiry["last_name"];
              echo $name ?: $enquiry["username"] ?: "Anonymous"
              ?>
            </strong>
            <em>
              <?php echo date("jS F, Y", strtotime($enquiry["created_at"])) ?>
            </em>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
</main>


<?php render_footer(); ?>
