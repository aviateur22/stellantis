<?php
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/interfaces/OrderRepositoryInterface.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/utils/validators.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/utils/StaticData.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/model/RepositoriesModel.php';

abstract class UpdateOrderModel {

  // N° de la commande a changer
  protected int $orderId;

  // Quantité
  protected int $orderQuantity;

  // Date de livraison
  protected string $orderDeliveredDate;

  /**
   * Repository Order
   *
   * @var OrderRepositoryInterface
   */
  protected OrderRepositoryInterface $orderRepository;

  function __construct(RepositoriesModel $repositories, int $orderId, string $orderDeliveredDate, int $orderQuantity) {
    $this->orderRepository = $repositories->getOrderRepository();
    $this->orderId = $orderId;
    $this->orderDeliveredDate = $orderDeliveredDate;
    $this->orderQuantity = $orderQuantity;    
  }

  /**
  * Mise a jour d'une commande
  *
  * @return void
  */
  abstract function updateOrder(): void;


  /**
   * Renvoie la commande mise a jour
   *
   * @param string $orderId
   * @return stdClass
   */
  function findUpdatedOrder(): array {
    $findOrder =  $this->orderRepository->findOne($this->orderId);

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
  public function findClassNameOrderColor($wipId): string {
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