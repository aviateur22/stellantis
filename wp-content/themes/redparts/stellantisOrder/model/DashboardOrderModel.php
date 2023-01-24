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

  function __construct(Order $order, array $quantitiesByDate
    ) {
      parent::__construct(
        $order->getOrderId(),
        $order->getCoverCode(),
        $order->getModel(),
        $order->getFamily(),
        $order->getOrderform(),
        $order->getOrderBuyer(),
        $order->getdeliveredDate(),
        $order->getQuantity(),
        $order->getPartNumber(),
        $order->getCoverLink(),
        $order->getOrderDate(),
        $order->getCountryCode(),
        $order->getCountryName(),
        $order->getWipId(),
        $order->getIsValid(),
        $order->getBrand(),
        $order->getVersion(),
        $order->getYear(),
        $order->getPrintForecast()
      );
      $this->quantitiesByDate = $quantitiesByDate;
  }
  #Region Getter

   /**
   * Renvoie les quantités par date
   *
   * @return array
   */
  function getQuantitiesByDate(): array {
    return $this->quantitiesByDate;
  }

#endRegion
}