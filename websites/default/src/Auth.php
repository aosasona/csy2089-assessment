<?php

namespace Trulyao\Eds;

use Trulyao\Eds\Models\User;

final class Auth
{
  public static function isLoggedIn(): bool
  {
    return isset($_SESSION["user_id"]) || isset($_COOKIE["user_id"]);
  }

  public static function login(User $user, bool $remember = false): void
  {
    $_SESSION["user_id"] = $user->id;
    if ($remember) {
      // remember for the next 30 days
      setcookie("user_id", $user->id, time() + 60 * 60 * 24 * 30);
    }
  }

  public static function logout(): void
  {
    unset($_SESSION["user_id"]);
    if (isset($_COOKIE["user_id"])) {
      setcookie("user_id", "", time() - 3600);
    }
  }

  public static function getUser(): ?User
  {
    $user_id = self::getLoggedInUserID();
    return User::findByPK($user_id);
  }

  private static function getLoggedInUserID(): int
  {
    if (isset($_SESSION["user_id"])) {
      return (int)$_SESSION["user_id"];
    } elseif (isset($_COOKIE["user_id"])) {
      return (int)$_COOKIE["user_id"];
    }
    throw new \Exception("User is not logged in");
  }
}
