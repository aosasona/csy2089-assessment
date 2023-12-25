<?php

namespace Trulyao\Eds\Models;

use Override;


class User extends BaseModel
{
  public ?int $id;
  public string $email;
  public string $first_name;
  public string $last_name;
  public string $username;
  protected string $password;
  public int $is_admin;
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

  #[Override]
  public function save(): bool
  {
    $this->first_name = ucwords(strtolower(trim($this->first_name)));
    $this->last_name = ucwords(strtolower(trim($this->last_name)));
    $this->username = strtolower(trim($this->username));
    return parent::save();
  }

  public function makeUsername(): string
  {
    $username = strtolower(substr(trim($this->first_name), 0, 1) . trim($this->last_name));
    $username = preg_replace("/[^a-z0-9]/", "", $username); // remove non-alphanumeric characters
    return $username;
  }
}
