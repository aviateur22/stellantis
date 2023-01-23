<?php
require_once ('/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/model/Order.php');

/**
 * Modele issue de la base de données
 */
class OrderEntity extends Order {
  /**
   * id de la commande
   *
   * @var int
   */
  protected int $id;

  function __construct(
    int $id,
    string $orderId,
    string $coverCode,
    string $model,
    string $family,
    string $orderFrom,
    string $orderBuyer,
    string $deliveredDate,
    string $quantity,
    string $partNumber,
    string $coverLink,
    string $orderDate,
    string $countryCode,
    string $countryName,
    string $wipId,
    bool $isValid,
    string $brand  
    ) {
    parent::__construct (
      $orderId,
      $coverCode,
      $model,
      $family,
      $orderFrom,
      $orderBuyer,
      $deliveredDate,
      $quantity,
      $partNumber,
      $coverLink,
      $orderDate,
      $countryCode,
      $countryName,
      $wipId,
      $isValid,
      $brand
    );
    
    $this->id = $id;
  }

  /**
   * Renvoie de id
   *
   * @return int
   */
  function getId(): int {
    return $this->id;
  }
}