<?php

namespace Trulyao\Eds\Models;

use Override;
use Trulyao\Eds\Utils;

final class Category extends BaseModel
{
  public ?int $id;
  public string $name;
  public string $slug;

  #[Override]
  public function save(): bool
  {
    $this->name = ucwords(strtolower(trim($this->name)));
    $this->slug = Utils::slugify($this->name);
    return parent::save();
  }
}
