<?php
require_once ('/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/services/MySqlForecastRepository.php');
require_once ('/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/interfaces/ForecastRepositoryInterface.php');
require_once ('/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/model/Order.php');
require_once ('/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/model/DashboardOrderModel.php');

class DashboardHelper {

  const INTERVAL_DAYS = 7;

  /**
   * Order repository
   *
   * @var OrderRepositoryInterface
   */
  protected OrderRepositoryInterface $orderRepository;

  /**
   * Undocumented variable
   *
   * @var array
   */
  protected array $dashboardOrders;

  /**
   * Undocumented variable
   *
   * @var array
   */
  protected array $quantitiesOrdersPerDay;

  function __construct($orderRepository)
  {
    $this->orderRepository = $orderRepository;
  }

/**
 * Récupération liste de commandes
 *
 * @param string|null $startDay
 * @param string $endDay
 * @return void
 */
  function setDashboardOrders(string $startDay = null, string $endDay ): void {

    // Récupération date départ
    if(empty($startDay)) {
      $startDay = date('Y-m-d 00:00:00');
    } else {
      $startDay = date('Y-m-d 00:00:00', strtotime($startDay));
    }
    
    // Date de fin
    $endDay = date('Y-m-d 00:00:00', strtotime($endDay));   
   
    // Liste des jours a afficher
    $this->quantitiesOrdersPerDay = $this->initializeQuantityOrderPerDay($startDay, $endDay);

    // Liste des commandes
    $this->dashboardOrders = $this->orderRepository->findOrdersOnIntervalDay($startDay, $endDay);

    $this->setOrdersQuantitiesPerDay();
  }

  /**
   * Renvoie les commanses a afficher
   *
   * @return void
   */
  public function getDashboardOrders() {
    return $this->dashboardOrders;
  }

  /**
   * Renvoie les commandes triés par jour
   *
   * @return void
   */
  public function getQuantitiesOrdersPerDay() {
    return $this->quantitiesOrdersPerDay;
  }

  /**
   * Renvoie une liste de date défini par 1 date de débu et une date de fin.
   *
   * @param string $startDay
   * @param string $endDay
   * @return array
   */
  private function initializeQuantityOrderPerDay(string $startDay, string $endDay): array {
    $intervalDay = [];

    // Initialisation de la date
    $calculatedDate = $startDay;

    do {

      // Ajout date au tableau
      $intervalDay[] = [
        'date' => date('d-M-Y', strtotime($calculatedDate)),
        'orders' => []
      ];
    
      //Actualisation de la date
      $calculatedDate = date('Y-m-d 00:00:00', strtotime($calculatedDate. '+1 day')); 
    }
    while(date('Y-m-d 00:00:00', strtotime($calculatedDate)) < date('Y-m-d 00:00:00', strtotime($endDay)));

    return $intervalDay;
  }

  /**
   * Ordonne les quantités par jour
   *
   * @param array $orders - liste des commandes
   * @param array $intervalDays - Liste des jours a afficher 
   * @return void
   */
  private function setOrdersQuantitiesPerDay() {

    foreach($this->dashboardOrders as $order) {
      for($i = 0; $i < count($this->quantitiesOrdersPerDay); $i++) {
        if(date('Y-m-d 00:00:00', strtotime($this->quantitiesOrdersPerDay[$i]['date'])) === date('Y-m-d 00:00:00', strtotime($order['deliveredDate']))){
            
          $this->quantitiesOrdersPerDay[$i]['orders'][] = [
            'partNumber' => $order['partNumber'],
            'deliveredDate' => $order['deliveredDate'],
            'quantity' => $order['quantity']
          ];
        }
      }    
    }
  }
}
