<?php

namespace Trulyao\Eds\Models;


class User extends BaseModel
{
  public ?int $id;
  public string $email;
  public string $first_name;
  public string $last_name;
  public string $username;
  private string $password;
  public bool $is_admin;
  public int $perm;
  public string $created_at;
  public string $last_updated_at;

  public function __get(string $name): mixed
  {
    return $this->{$name};
  }

  public function __set(string $name, mixed $value): void
  {
    if ($name == "password") {
      $this->setPassword($value);
      return;
    }

    parent::__set($name, $value);
  }

  public function can(Permission ...$permissions): bool
  {
    foreach ($permissions as $p) {
      if (!($this->perm & $p->value)) {
        return false;
      }
    }

    return true;
  }

  public function setPassword(string $password): void
  {
    $this->password = password_hash($password, PASSWORD_DEFAULT);
  }

  public function verifyPassword(string $password): bool
  {
    return password_verify($password, $this->password);
  }

  public function updatePassword(string $password): void
  {
    $this->setPassword($password);
    $this->query("UPDATE `users` SET `password` = ? WHERE `id` = ?", [$this->password, $this->id]);
  }
}
