<?php

namespace Trulyao\Eds\Models;

enum Permission: int
{
  case Read = 1;
  case Write = 2;
  case Delete = 4;
  case ManageUsers = 8;
  case All = 15;

  public static function fromValue(int|string $value): self
  {
    return match ((int)$value) {
      1 => self::Read,
      2 => self::Write,
      4 => self::Delete,
      8 => self::ManageUsers,
      15 => self::All,
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
}
