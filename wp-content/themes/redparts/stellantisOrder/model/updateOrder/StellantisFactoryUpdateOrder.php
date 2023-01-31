<?php
require_once ('/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/model/updateOrder/UpdateOrderModel.php');
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/interfaces/OrderRepositoryInterface.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/utils/validators.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/utils/StaticData.php';
require_once ('/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/model/RepositoriesModel.php');

class StellantisFactoryUpdateOrder extends UpdateOrderModel {

  function __construct(RepositoriesModel $repositories, int $orderId, string $orderDeliveredDate, int $orderQuantity) {
    parent::__construct($repositories, $orderId, $orderDeliveredDate, $orderQuantity);    
  }

  /**
   * Mise a jour d'une commande
   *
   * @return void
   */
  function updateOrder(): void {
    $this->orderRepository->update($this->orderId, $this->orderQuantity, $this->orderDeliveredDate);
  }

}