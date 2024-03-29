<?php

namespace Trulyao\Eds;

final class Utils
{
  public static function slugify(string $str): string
  {
    $str = strtolower($str);
    $str = preg_replace("/[^a-z0-9\s-]/", "", $str); // remove invalid chars (only keep A-Z, 0-9, whitespace of any type and hyphen)
    $str = preg_replace("/[\s_-]+/", " ", $str); // replace multiple spaces/hyphens/underscores with a single hyphen
    $str = preg_replace("/\s+/", "-", $str); // replace spaces with hyphens
    return $str;
  }
}
