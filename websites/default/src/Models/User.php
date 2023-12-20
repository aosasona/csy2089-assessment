<?php

namespace Trulyao\Eds\Models;


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

  public function can(Permission ...$permissions): bool
  {
    foreach ($permissions as $p) {
      if (!($this->perm & $p->value)) {
        return false;
      }
    }

    return true;
  }
}
