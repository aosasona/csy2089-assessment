<?php

use Trulyao\Eds\ClientException;

require_once __DIR__ . "/../../src/prelude.php";

if (isset($_POST["create-category"])) {
  try {
    // TODO: handle this
  } catch (Exception $e) {
    handle_throwable($e, "/create/category.php");
  } finally {
  }
}
render_header("New category");

?>

<h2>New category</h2>

<form method="POST" class="mt-4">
  <?php render_error(); ?>
  <div class="form-control">
    <label for="name">Name</label>
    <input type="text" name="name" id="name" class="w-1/2" placeholder="Consoles, TVs etc" />
  </div>

  <button name="create-category" type="submit">Save</button>
</form>

<?php
render_footer();
