<?php

require_once __DIR__ . "/../../src/prelude.php";

render_header("New product");
?>

<h2>New product</h2>

<form method="POST" class="mt-4">
  <div class="form-control">
    <label for="name">Name</label>
    <input type="text" name="name" id="name" placeholder="20W Anker charger" />
  </div>

  <button type="submit">Save</button>
</form>

<?php
render_footer();
