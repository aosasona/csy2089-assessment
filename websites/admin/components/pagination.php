<?php

/**
 * @var int $current_count - the number of items on the current page
 * @var int $total_count - the total number of items
 * @var int $current_page - the current page number
 */
?>
<?php if ($current_count > 0 && $current_page <= 1) : ?>
  <center class="mt-8">
    <div class="pagination">
      <?php if ($current_page > 1) : ?>
        <a href="?page=<?php echo $current_page - 1 ?>" class="previous"><i class="ti ti-chevron-left"></i></a>
      <?php endif; ?>

      <form>
        <input type="number" name="page" value="<?php echo $current_page ?>" class="page-input" <?php echo $total_count < PAGE_SIZE ? "disabled" : "" ?> />
      </form>

      <?php if ($current_page * PAGE_SIZE < $total_count) : ?>
        <a href="?page=<?php echo $current_page + 1 ?>" class="next"><i class="ti ti-chevron-right"></i></a>
      <?php endif; ?>
    </div>
  </center>
<?php endif; ?>
