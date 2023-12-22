<?php

namespace Trulyao\Eds\Models;

use Override;

class Product extends BaseModel
{
  public ?int $id;
  public string $public_id;
  public string $name;
  public string $description;
  public int $price;
  public string $image_name;
  public string $manufacturer;
  public int $category_id;
  public int $is_listed;
  public int $is_featured;
  public string $listed_by;
  public string $created_at;
  public string $last_updated_at;

  #[Override]
  public function save(): bool
  {
    $this->name = ucwords(strtolower(htmlspecialchars($this->name)));
    $this->description = ucfirst(htmlspecialchars($this->description));
    $this->manufacturer = ucwords(strtolower(htmlspecialchars($this->manufacturer)));
    return parent::save();
  }
}
