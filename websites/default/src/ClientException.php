<?php

namespace Trulyao\Eds;

use Exception;

final class ClientException extends Exception
{
  public function __construct(string $message, int $code = 400)
  {
    parent::__construct($message, $code);
  }
}
