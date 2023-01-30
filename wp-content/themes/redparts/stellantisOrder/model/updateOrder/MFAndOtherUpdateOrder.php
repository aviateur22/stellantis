<?php
require_once ('/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/model/updateOrder/UpdateOrderModel.php');
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/interfaces/OrderRepositoryInterface.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/services/MySqlOrderRepository.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/utils/validators.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/utils/StaticData.php';

class MFAndOtherUpdateOrder extends UpdateOrderModel {

  /**
   * StatutId de la commande
   *
   * @var integer
   */
  protected int $statusId;

  function __construct(OrderRepositoryInterface $orderRepository, int $orderId, string $orderDeliveredDate, int $orderQuantity, int $statusId)
  {
    parent::__construct($orderRepository, $orderId, $orderDeliveredDate, $orderQuantity);

    $this->statusId = $statusId;    
  }

  /**
  * Mise a jour d'une commande
  *
  * @return void
  */
  function updateOrder(): void {
    $this->orderRepository->update($this->orderId, $this->orderQuantity, $this->orderDeliveredDate, $this->statusId);
  }


}