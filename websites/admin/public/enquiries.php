<?php


require_once __DIR__ . "/../src/prelude.php";

use Trulyao\Eds\Auth;
use Trulyao\Eds\ClientException;
use Trulyao\Eds\Models\Enquiry;
use Trulyao\Eds\Models\Permission;

define("PAGE_SIZE", 50);

require_auth();
ensure_user_can(Permission::Read);

if (isset($_POST["add_answer"])) {
  try {
    $enquiry_id = $_POST["enquiry_id"];
    if (empty($enquiry_id)) {
      throw new Exception("Enquiry ID is required");
    }

    $enquiry = Enquiry::findByPK($enquiry_id);
    if (empty($enquiry)) {
      throw new ClientException("Enquiry not found");
    }

    $enquiry->answer = htmlspecialchars($_POST["answer"]);
    $enquiry->answered_by = Auth::getUser()->id;
    $enquiry->save();

    redirect($_SERVER["REQUEST_URI"]);
  } catch (\Throwable $e) {
    handle_throwable($e, $_SERVER["REQUEST_URI"]);
  }
} elseif (isset($_POST["toggle_publishing_status"])) {
  try {
    $enquiry_id = $_POST["enquiry_id"];
    if (empty($enquiry_id)) {
      throw new Exception("Enquiry ID is required");
    }

    $publishing_status = !empty($_POST["is_published"]) && $_POST["is_published"] == "on" ? 1 : 0;
    $enquiry = Enquiry::findByPK($enquiry_id);
    if (empty($enquiry)) {
      throw new ClientException("Enquiry not found");
    }

    $enquiry->is_published = $publishing_status;
    $enquiry->save();

    redirect($_SERVER["REQUEST_URI"]);
  } catch (\Throwable $e) {
    handle_throwable($e, $_SERVER["REQUEST_URI"]);
  }
}


$page = $_GET["page"] ?? 1;
$show_unanswered = $_GET["show_unanswered"] ?? false;
$enquiries = Enquiry::paginateWithQuery(
  sprintf(
    "SELECT 
    e.*, p.`name` AS `product_name`, COALESCE(CONCAT(u.`first_name`, ' ', u.`last_name`), 'Anonymous') AS `asked_by_name`
    FROM `enquiries` e 
    LEFT JOIN `products` p ON p.`id` = e.`product_id` 
    LEFT JOIN `users` u ON u.`id` = e.`asked_by` 
    %s 
    ORDER BY `created_at` DESC",
    $show_unanswered ? "WHERE `answer` IS NULL" : ""
  ),
  $page,
  PAGE_SIZE,
);
$page_count = Enquiry::getPageCount(PAGE_SIZE);


render_header("Enquiries");
?>

<main class="mt-4">
  <div class="flex justify-end">
    <form method="get">
      <div class="form-control has-toggle">
        <div class="toggle">
          <input type="checkbox" name="show_unanswered" id="show_unanswered" onchange="this.form.submit()" <?php echo $show_unanswered ? "checked" : "" ?> />
          <label for="show_unanswered"></label>
        </div>
        <p>Show unanswered <b>ONLY</b></p>
      </div>
    </form>
  </div>

  <?php if (empty($enquiries)) : ?>
    <div class="no-items">
      <p>There are no enquiries.</p>
    </div>
  <?php else : ?>

    <ul class="enquiries">
      <?php render_error(); ?>
      <?php foreach ($enquiries as $enquiry) : ?>
        <li>
          <div class="flex justify-between items-center">
            <p class="meta">
              Asked by <b><?php echo $enquiry["asked_by_name"] ?></b> for <b><?php echo strlen($enquiry["product_name"]) > 50 ? substr($enquiry["product_name"], 0, 50) . "..." : $enquiry["product_name"] ?></b>
              <?php echo empty($enquiry["is_published"]) ? "(pending approval)" : "" ?>
            </p>

            <!-- Draft or publish -->
            <form method="post" class="">
              <input type="hidden" name="toggle_publishing_status" value="toggle_publishing_status" />
              <input type="hidden" name="enquiry_id" value="<?php echo $enquiry["id"] ?>" />
              <div class="form-control has-toggle mt-2">
                <div class="toggle">
                  <input type="checkbox" name="is_published" id="is_published_<?php echo $enquiry['id'] ?>" onchange="this.form.submit()" <?php echo !empty($enquiry["is_published"]) && $enquiry["is_published"] == 1 ? "checked" : "" ?> />
                  <label for="is_published_<?php echo $enquiry['id'] ?>"></label>
                </div>
                <p><b>Publish</b></p>
              </div>
            </form>

          </div>

          <p class="mt-4"><b>Q:</b> <?php echo $enquiry["question"] ?></p>

          <?php if (!empty($enquiry["answer"])) : ?>
            <p class="mt-2"><b>A:</b> <?php echo $enquiry["answer"] ?></p>
          <?php else : ?>
            <form method="post">
              <input type="hidden" name="enquiry_id" value="<?php echo $enquiry["id"] ?>" />
              <div class="form-control flex items-start gap-4 mt-4">
                <textarea name="answer" rows="1" resize="vertical" placeholder="Type your answer here..." class="w-full"></textarea>
                <button name="add_answer" type="submit" class="btn btn-primary ml-4">Answer</button>
              </div>
            </form>
          <?php endif; ?>

        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>

  <?php render_pagination(count($enquiries), $page_count, $page); ?>
</main>

<?php render_footer(); ?>
