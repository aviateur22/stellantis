<?php
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/utils/validators.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/utils/StaticData.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/interfaces/OrderRepositoryInterface.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/services/MySqlOrderRepository.php';

/**
 * Modification d'une commande sur le dashboard
 */
class UpdateDashboardOrderHelper {

  /**
   * orderRespository
   *
   * @var OrderRepositoryInterface
   */
  protected OrderRepositoryInterface $orderRepository;

  function __construct(OrderRepositoryInterface $orderRepository) {
    $this->orderRepository = $orderRepository;
  }

 /**
  * Mise a jour d'une commande
  *
  * @param string $orderId
  * @param string $quantity
  * @param string $deliveredDate
  * @param string $status
  * @return void
  */
  function updateOrder(string $orderId, string $quantity, string $deliveredDate, string $status) {
    $this->orderRepository->update($orderId, $quantity, $deliveredDate, $status);
  }


  /**
   * Renvoie la commande mise a jour
   *
   * @param string $orderId
   * @return stdClass
   */
  function findUpdatedOrder(string $orderId): stdClass {
    $findOrder =  $this->orderRepository->findOne($orderId);

    if(count($findOrder) === 0) {
      throw new \Exception('Updated command not find', 500);
    }

    return $findOrder[0];

  }

  /**
   * Recherche du nom de la class de la commande
   *
   * @param string $wipId - id du statut
   * @return string
   */
  function findClassNameOrderColor(string $wipId): string {
    $orderClassName = findColorOrderDisplay($wipId);
    return $orderClassName;
  }

  /**
   * Recherche de tous les nom de class existant
   *
   * @return array
   */
  function findColorClassToRemove(): array {
    $colorClassToRemove = [];

    foreach(StaticData::CLASS_NAME_ORDERS_COLORS as $color) {
      $colorClassToRemove[] = $color;
    }

    return $colorClassToRemove;
  }
}