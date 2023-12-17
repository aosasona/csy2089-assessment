<?php

namespace Trulyao\Eds\Models;

use Trulyao\Eds\Database;
use PDO;

enum Condition: string
{
  case AND = "AND";
  case OR = "OR";
}

abstract class BaseModel
{
  private PDO $db;

  public  const OR = "OR";
  public const AND = "AND";

  /**
   * @throws \Exception
   */
  public function __construct(?Database $instance = null)
  {
    if (!empty($instance)) {
      $this->db = $instance->getConnection();
    } else {
      $this->db = Database::getInstance()->getConnection();
    }
  }

  public static function getTableName(): string
  {
    return self::pluralize(strtolower((new \ReflectionClass(static::class))->getShortName()));
  }

  /**
   * @param array<int,mixed> $filters
   */
  public static function exists(array $filters, Condition $condition = Condition::AND): bool
  {
    $where_parts = [];
    if (empty($filters)) {
      throw new \Exception("Filters cannot be empty");
    }
    foreach (array_keys($filters) as $key) {
      if (!property_exists(static::class, $key)) {
        throw new \Exception("Column {$key} does not exist on model `" . self::getTableName() . "`");
      }

      $where_parts[] = "{$key} = :{$key}";
    }

    $where = implode(" {$condition->value} ", $where_parts);
    $sql = "SELECT COUNT(*) FROM " . self::getTableName() . " WHERE {$where}";
    $stmt = self::getConnection()->prepare($sql);
    $stmt->execute($filters);
    return $stmt->fetchColumn() > 0;
  }

  public static function findById(int $id): ?self
  {
    $sql = "SELECT * FROM " . self::getTableName() . " WHERE id = ?";
    $stmt = self::getConnection()->prepare($sql);
    $stmt->execute([$id]);
    $stmt->setFetchMode(\PDO::FETCH_CLASS, static::class);
    return $stmt->fetch() ?: null;
  }

  public static function findBy(string $column, mixed $value): ?self
  {
    if (!property_exists(static::class, $column)) {
      throw new \Exception("Column {$column} does not exist on this model (" . self::getTableName() . ")");
    }

    $sql = "SELECT * FROM " . self::getTableName() . " WHERE {$column} = ?";
    $stmt = self::getConnection()->prepare($sql);
    $stmt->execute([$value]);
    $stmt->setFetchMode(PDO::FETCH_CLASS, static::class);
    return $stmt->fetch() ?: null;
  }
  /**
   * @return array|bool
   */
  public static function paginate(int $page, int $limit): array
  {
    $offset = ($page - 1) * $limit;
    $sql = "SELECT * FROM " . self::getTableName() . " LIMIT {$limit} OFFSET {$offset}";
    $stmt = self::getConnection()->prepare($sql);
    $stmt->execute();
    $stmt->setFetchMode(\PDO::FETCH_CLASS, static::class);
    return $stmt->fetchAll() ?: [];
  }

  /**
   * @param  array<int,mixed> $params
   * @return array|bool
   */
  public static function query(string $sql, array $params = []): array
  {
    $stmt = self::getConnection()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
  }

  /**
   * @param       array<int,mixed> $params
   * @description an escape hatch for executing raw sql queries
   */
  public static function execute(string $sql, array $params = []): bool
  {
    $stmt = self::getConnection()->prepare($sql);
    return $stmt->execute($params);
  }
  /**
   * @return array|bool
   */
  public static function all(): array
  {
    $sql = "SELECT * FROM " . self::getTableName();
    $stmt = self::getConnection()->prepare($sql);
    $stmt->execute();
    $stmt->setFetchMode(\PDO::FETCH_CLASS, static::class);
    return $stmt->fetchAll();
  }
  /**
   * @param  array<int,mixed> $params
   * @return array|bool
   */
  public static function where(string $expr, array $params): array
  {
    $sql = "SELECT * FROM " . self::getTableName() . " WHERE {$expr}";
    $stmt = self::getConnection()->prepare($sql);
    $stmt->execute($params);
    $stmt->setFetchMode(\PDO::FETCH_CLASS, static::class);
    return $stmt->fetchAll();
  }


  public static function lastInsertedId(): int
  {
    return self::getConnection()->lastInsertId();
  }

  public static function count(): int
  {
    $stmt = self::getConnection()->prepare("SELECT COUNT(*) FROM " . self::getTableName());
    $stmt->execute();
    return $stmt->fetchColumn();
  }


  public function delete(): bool
  {
    $sql = "DELETE FROM " . self::getTableName() . " WHERE id = ?";
    $stmt = $this->db->prepare($sql);
    return $stmt->execute([$this->{self::getPrimaryKeyColumn()}]);
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

  /**
   * @param   array<string>|null $forceUpdate
   * @example ```
   * $old = User::findById(1);
   * $user = new User();
   * $user->id = $old->id;
   * $user->username = "new_username";
   * $user->update();
   * ```
   * */

  public function update(?array $forceUpdate = null): bool
  {
    $values = [];
    $columns = [];
    foreach ($this->getValues() as $key => $value) {
      // if the key is in the forceUpdate array, add it to the update query even if the value is null
      if (($value !== null && !in_array($key, $forceUpdate ?? [])) && $key !== self::getPrimaryKeyColumn()) {
        $values[$key] = $value;
        $columns[] = "{$key} = :{$key}";
      } elseif ($key === self::getPrimaryKeyColumn()) {
        // if the key is the primary key, add it to the values array ONLY because we don't want to update the primary key
        $values[$key] = $value;
      }
    }

    if (empty($columns)) {
      return false;
    } elseif (!isset($values[self::getPrimaryKeyColumn()])) {
      throw new \Exception("Cannot update record without primary key, if the primary key for this model is not `id` override the self::getPrimaryKeyColumn() method");
    }

    $sql = "UPDATE {$this->getTableName()} SET {$columns} WHERE " . self::getPrimaryKeyColumn() . " = :id";
    $stmt = $this->db->prepare($sql);
    return $stmt->execute($values);
  }

  /**
   * @description Useful for updating a model with a partial model, for example when updating a user profile and only updating the fields that were changed
   * */
  public function merge(self $model): self
  {
    foreach ($this->getAttributes() as $attr) {
      if ($model->{$attr} !== null) {
        $this->{$attr} = $model->{$attr};
      }
    }
    return $this;
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

  // Only to be used in static methods
  protected static function getConnection(): PDO
  {
    return Database::getInstance()->getConnection();
  }

  protected static function getPrimaryKeyColumn(): string
  {
    return "id";
  }


  private static function pluralize(string $word): string
  {
    if (str_ends_with($word, "y")) {
      return substr($word, 0, -1) . "ies";
    }
    return $word . "s";
  }
}
