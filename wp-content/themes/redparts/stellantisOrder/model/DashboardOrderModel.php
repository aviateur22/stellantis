<?php
require_once ('/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/model/Order.php');
/**
 * modèle OrderDashboard
 */
class DashboardOrderModel extends Order {

  /**
   * Liste de quantité par jour 
   *
   * @var array
   */
  protected array $quantitiesByDate; 

  function __construct(
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
    string $wip,
    bool $isValid,
    array $quantitiesByDate
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
      $wip,
      $isValid
    );
    
    $this->quantitiesByDate = $quantitiesByDate;
  }

  /**
   * Renvoie les quantités par date
   *
   * @return array
   */
  function getQuantitiesByDate(): array {
    return $this->quantitiesByDate;
  }

}