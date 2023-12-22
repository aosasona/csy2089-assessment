<?php

namespace Trulyao\Eds\Models;

final class Enquiry extends BaseModel
{
  public ?int $id;
  public string $product_id;
  public ?int $asked_by;
  public ?int $answered_by;
  public string $question;
  public string $answer;
  public int $is_published;
  public string $created_at;
  public string $last_updated_at;
}
