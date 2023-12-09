<?php

namespace Trulyao\Eds\Models;

use Trulyao\Eds\Database;
use PDO;

abstract class BaseModel
{
  private static PDO $db;

  /**
   * @throws \Exception
   */
  public function __construct(?Database $instance = null)
  {
    if ($instance) {
      self::$db = $instance->getConnection();
      return;
    }

    self::$db = Database::getInstance()->getConnection();
  }

  // restrict setting properties that don't exist and certain internal properties even though they behave like public properties
  public function __set(string $name, mixed $value): void
  {
    if (!property_exists($this, $name)) {
      throw new \Exception("Property {$name} does not exist on this model ({$this->getTableName()})");
    } elseif ($name === "id") {
      throw new \Exception("Cannot set id property");
    } elseif ($name === "created_at" || $name === "updated_at") {
      throw new \Exception("Cannot set {$name} property, this is set automatically");
    } elseif (is_array($value) || is_object($value)) {
      throw new \Exception("{$name} must be a scalar value, no arrays or objects allowed");
    }

    $this->{$name} = $value;
  }

  public function getAttributes(): array
  {
    $all_vars = array_keys(get_object_vars($this));
    $internal_vars = ["db"];
    return array_diff($all_vars, $internal_vars);
  }

  public function getValues(): array
  {
    $values = [];
    foreach ($this->getAttributes() as $attr) {
      $values[$attr] = $this->{$attr};
    }
    return $values;
  }

  public static function getTableName(): string
  {
    return self::pluralize(strtolower((new \ReflectionClass(static::class))->getShortName()));
  }

  public static function findById(int $id): ?self
  {
    $sql = "SELECT * FROM " . self::getTableName() . " WHERE id = ?";
    $stmt = self::$db->prepare($sql);
    $stmt->execute([$id]);
    $stmt->setFetchMode(\PDO::FETCH_CLASS, static::class);
    return $stmt->fetch();
  }

  public function save(): bool
  {
    $values = array_filter($this->getValues(), fn ($value) => $value !== null);
    $columns = implode(", ", array_keys($values));
    $placeholders = implode(", ", array_map(fn ($col) => ":{$col}", array_keys($values)));
    $sql = "INSERT INTO {$this->getTableName()} ({$columns}) VALUES ({$placeholders})";
    $stmt = $this->db->prepare($sql);
    return $stmt->execute($values);
  }


  private static function pluralize(string $word): string
  {
    if (str_ends_with($word, "y")) {
      return substr($word, -1) . "ies";
    }
    return $word . "s";
  }
}
