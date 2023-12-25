<?php

namespace Trulyao\Eds\Models;

final class Enquiry extends BaseModel
{
  public ?int $id;
  public string $product_id;
  public ?int $asked_by;
  public ?int $answered_by;
  public string $question;
  public ?string $answer;
  public int $is_published;
  public string $created_at;
  public string $last_updated_at;

  public static function ask(int $product_id,  string $question, ?int $asked_by = null, ?bool $defer_publishing = false): void
  {
    $enquiry = new Enquiry();
    $enquiry->product_id = $product_id;
    $enquiry->asked_by = $asked_by;
    $enquiry->question = $question;
    $enquiry->is_published = !$defer_publishing;
    $enquiry->save();
  }

  public static function forProduct(int $product_id): array
  {
    $sql = "SELECT 
    e.*, u.`first_name`, u.`last_name`, u.`username`
    FROM `enquiries` e 
    LEFT JOIN `users` u ON u.`id` = e.`asked_by` 
    WHERE e.`product_id` = :product_id AND e.`is_published` = 1 
    ORDER BY e.`created_at` DESC";

    $params = ["product_id" => $product_id];
    return self::query($sql, $params);
  }
}
