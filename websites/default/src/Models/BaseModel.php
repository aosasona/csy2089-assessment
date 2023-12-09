<?php

namespace Trulyao\Eds\Models;

use Trulyao\Eds\Database;

abstract class BaseModel
{
  private Database $db;

  /**
   * @throws \Exception
   */
  public function __construct()
  {
    $this->db = Database::getInstance();
  }

  public function getTableName(): string
  {
    return $this->pluralize(strtolower((new \ReflectionClass($this))->getShortName()));
  }

  private function pluralize(string $word): string
  {
    if (str_ends_with($word, "y")) {
      return substr($word, -1) . "ies";
    }
    return $word . "s";
  }
}
