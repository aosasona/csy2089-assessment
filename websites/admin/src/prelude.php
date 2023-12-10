<?php

require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../../default/vendor/autoload.php";

if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}

function redirect(string $url): void
{
  header("Location: $url", true, 303);
  exit;
}

function dd($var): void
{
  var_dump($var);
  exit;
}

function render_header(string $title): void
{
  include_once "../components/header.php";
}

function render_footer(): void
{
  include_once "../components/footer.php";
}
