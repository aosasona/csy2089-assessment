<?php

use Trulyao\Eds\Auth;
use Trulyao\Eds\Models\Category;

/**
 * @var title string
 */

$title = $title ?: "Ed's Electronics";

$categories = Category::all();
?>

<!doctype html>
<html>

<head>
  <title><?php echo $title ?></title>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width" />
  <link rel="stylesheet" href="/css/electronics.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
</head>

<body>
  <header>
    <h1>Ed's Electronics</h1>

    <ul>
      <li><a href="/">Home</a></li>
      <li>
        Products
        <ul>
          <?php foreach ($categories as $category) : ?>
            <li><a href="/category/<?php echo $category->slug ?>"><?php echo $category->name ?></a></li>
          <?php endforeach; ?>
        </ul>
      </li>
      <?php if (!Auth::isLoggedIn()) : ?>
        <li><a href="/auth.php">Sign In</a></li>
      <?php else : ?>
        <li><a href="/auth.php?logout">Sign Out</a></li>
      <?php endif; ?>
    </ul>

    <address>
      <p>
        We are open 9-5, 7 days a week. Call us on
        <strong>01604 11111</strong>
      </p>
    </address>
  </header>
