<?php

/**
 * @var string $id
 **/

// prevent direct access to this page
if (!isset($id)) {
  header("Location: /");
  exit;
}

echo "Product $id";
