<?php

require_once __DIR__ . "/../src/prelude.php";

render_header("Categories");
?>

<main class="mt-4">
  <div class="w-full flex justify-end">
    <a href="/manage/category.php" class="btn" title="Create new category">New category</a>
  </div>
</main>

<?php
render_footer();
