<?php

namespace Trulyao\Eds\Models;

class Product extends BaseModel
{
  public ?int $id;
  public string $public_id;
  public string $name;
  public string $description;
  public int $price;
  public string $image_name;
  public int $category_id;
  public int $is_listed;
  public int $is_featured;
  public string $listed_by;
  public string $created_at;
  public string $last_updated_at;
}
