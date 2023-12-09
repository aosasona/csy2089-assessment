<?php

namespace Trulyao\Eds\Models;

enum Permission: int
{
  case Read = 1;
  case Write = 2;
  case Delete = 4;
  case ManageUsers = 8;
  case All = 15;

  public static function fromValue(int|string $value): Permission
  {
    return match ((int)$value) {
      1 => Permission::Read,
      2 => Permission::Write,
      4 => Permission::Delete,
      8 => Permission::ManageUsers,
      15 => Permission::All,
      default => throw new \Exception("Invalid permission value"),
    };
  }
  /**
   * @param array<int,mixed> $values
   */
  public static function fromMultiple(array $values): Permission
  {
    $value = 0;
    foreach ($values as $v) {
      $value |= (int)$v;
    }
    return Permission::from($value);
  }
};

class User extends BaseModel
{
  public ?int $id;
  public string $email;
  public string $first_name;
  public string $last_name;
  public string $username;
  public string $password;
  public bool $is_admin;
  public int $perm;
  public string $created_at;
  public string $last_updated_at;

  public function __get(string $name): mixed
  {
    if ($name === "perm" || $name == "permissions") {
      return Permission::fromValue($this->perm);
    }
    return $this->{$name};
  }
}
