<?php
/**
 * 
 */
class CreateDashboardOrdersHelper {
  /**
   * Liste des commandes extraites de la base de données
   *
   * @var array
   */
  protected array $orders = [];

 
  /**
   * Liste des commandes sur le dashborad
   */
  protected array $dashboardOrders = [];

  /**
   * Liste contant les jours du tableau a afficher
   *
   * @var array
   */
  protected array $intervalDays;  

  /**
   * Itération sur la liste orders afin de retrouver toutes les commandes ayant un partNumber donné
   *
   * @param string $partNumber
   * @return void
   */
  protected function iterateThroughOrders(string $partNumber, array &$quantityByDateArray, string $orderBuyer) {
    foreach($this->orders as $order) {      

      // Vérification format de la données
      if(!$order instanceof OrderEntity) {
        throw new \Exception('Error Instanceof model - CreateDashboardOrdersHelper', 500);
      }

      if($order->getPartNumber() === $partNumber && $order->getOrderBuyer() === $orderBuyer) {

        $deliveredDate = date('Y-m-d 00:00:00', strtotime($order->getdeliveredDate()));

        $order = [
          'pocketCode' => $order->getCoverCode(),
          'countryName' => $order->getCountryName(),
          'countryCode' => $order->getCountryCode(),
          'family' => $order->getFamily(),
          'wipId' => $order->getWipId(),
          'quantity' => $order->getQuantity(),
          'id' => $order->getId()
        ];
      
        // Rajoute les données de la commandes dans le tableau de datz
        for($i = 0; $i < count($quantityByDateArray); $i++) {
          $dateInArray = date('Y-m-d 00:00:00', strtotime($quantityByDateArray[$i]['day']));
          // var_dump($quantity['day']);
          if($dateInArray === $deliveredDate) {
            // var_dump('ici');
            $quantityByDateArray[$i]['order'] = $order;
            // var_dump($partNumber);
            // var_dump($quantityByDateArray[$i]);
          }
        };      
      }
    }
    // var_dump($quantityByDateArray);
  }

  /**
   * Renvoie un nouveau DashboardOrder model 
   *
   * @param OrderEntity $order
   * @param array $quantityByDateArray
   * @return DashboardOrderModel
   */
  protected function createDashboardOrderModel(OrderEntity $order, array $quantityByDateArray): DashboardOrderModel {
    return new DashboardOrderModel($order, $quantityByDateArray);
  }

  /**
   * Creation d'un nouveau tableau quantité par jour
   *
   * @return array
   */
  protected function createQuantityByDateArray(): array {
    $orderPerDay = [];

    foreach($this->intervalDays as $day) {
      $orderPerDay[] = [
        'day' => $day['date'],
        'order' => []
      ];
    }

    return $orderPerDay;
  }

  /**
   * Vérification si PartNumber dans dashbordOrders
   *
   * @param string $partNumber
   * @return bool
   */
  protected function isParNumberInDashboardArray(string $partNumber, string $orderBuyer): bool {
    foreach($this->dashboardOrders as $order) {
      
      // Erreur dormat de données
      if(!$order instanceof DashboardOrderModel) {
        throw new \Exception('Error Instanceof model - CreateDashboardOrdersHelper', 500);
      }
            
      if($order->getPartNumber() === $partNumber && $order->getOrderBuyer() === $orderBuyer) {
        return true;
      }
    }
    return false;
  }
}