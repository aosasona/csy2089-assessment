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

  <div>
    <?php if (count($categories) === 0) : ?>
      <p class="no-items">No categories found</p>
    <?php else : ?>
      <div class="container">
        <table class="list">
          <thead>
            <tr>
              <th scope="col">Category name</th>
              <th scope="col"></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($categories as $category) : ?>
              <tr>
                <th scope="row">
                  <?php echo $category->name ?>
                </th>
                <td class="actions">
                  <a href="/manage/category.php?id=<?php echo $category->id; ?>" class="action">
                    <i class="ti ti-edit"></i>
                    <span>edit</span>
                  </a>

                  <a href="/manage/category.php?id=<?php echo $category->id; ?>&action=delete" class="action delete" data-confirm="Are you sure you want to delete this category? THIS ACTION CANNOT BE REVERSED.">
                    <i class="ti ti-trash"></i>
                    <span>delete</span>
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>



    <?php endif; ?>
  </div>
</main>

<?php
render_footer();
