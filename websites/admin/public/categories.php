<?php

use Trulyao\Eds\Models\Category;

require_once __DIR__ . "/../src/prelude.php";

define("PAGE_SIZE", 25);

$page = $_GET["page"] ?? 1;
$categories = Category::paginate($page, PAGE_SIZE);
$page_count = Category::getPageCount(PAGE_SIZE);

render_header("Categories");
?>

<main class="mt-4">
  <div class="w-full flex justify-end">
    <a href="/manage/category.php" class="btn" title="Create new category">New category</a>
  </div>
</main>

<?php
render_footer();
