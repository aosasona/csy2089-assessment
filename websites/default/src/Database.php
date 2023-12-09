<?php

namespace Trulyao\Eds;

use PDO;

final class Database
{
  private PDO $connection;
  private static ?self $instance = null;

  private function __construct()
  {
    $conn = new PDO("mysql:dbname=eds_electronics;host=mysql", "v.je", "v.je");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $this->connection = $conn;
  }

  public function getConnection(): PDO
  {
    return $this->connection;
  }

  /**
   * @return Database
   */
  public static function getInstance(): Database
  {
    if (self::$instance === null) {
      self::$instance = new Database();
    }

    return self::$instance;
  }
}
