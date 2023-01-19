<?php
require_once ('/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/services/MySqlForecastRepository.php');
require_once ('/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/interfaces/ForecastRepositoryInterface.php');
require_once ('/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/model/Order.php');
require_once ('/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/model/DashboardOrderModel.php');
require_once ('/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/model/OrderEntity.php');
require_once ('/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/helpers/CreateDashboardOrdersHelper.php');

class DashboardHelper extends CreateDashboardOrdersHelper {

  const INTERVAL_DAY = 7;

  /**
   * Order repository
   *
   * @var OrderRepositoryInterface
   */
  protected OrderRepositoryInterface $orderRepository;

  function __construct($orderRepository)
  {
    $this->orderRepository = $orderRepository;
  }

  /**
   * Undocumented function
   *
   * @param string|null $startDay
   * @param string $endDay
   * @return void
   */
  public function setDashboardOrders(string $startDay = null, string $endDay) {
    // Formate les dates de début et de fin
    $this->formatDayInterval($startDay, $endDay);

    // Récupéaration de la liste des dates de l'interval 
    $this->setIntervalDayArray($startDay, $endDay);

    // Récupéaration des commandes en base de donnée
    $this->getOrders($startDay, $endDay);

    // Récupération
    $this->formatDashboardOrders();    
  }

  /**
   * Renvoie les commandes a afficher
   *
   * @return array
   */
  public function getDashboardOrders(): array {
    return $this->dashboardOrders;
  }

  /**
   * Renvoie les commandes triés par jour
   *
   * @return array
   */
  public function getIntervalDays(): array {
    return $this->intervalDays;
  }


  /**
   * 
   * @param string $startDay
   * @param string $endDay
   * @return void
   */
  private function formatDayInterval(string &$startDay = null, string &$endDay ) {
    // Récupération date départ
    if(empty($startDay)) {
      $startDay = date('Y-m-d 00:00:00');
    } else {
      $startDay = date('Y-m-d 00:00:00', strtotime($startDay));
    }    

    // Date de fin
    $endDay = date('Y-m-d 00:00:00', strtotime('+'.(self::INTERVAL_DAY - 1).' day', strtotime($startDay)));   
    
    // $endDay = date('Y-m-d 00:00:00', strtotime($endDay));    
  }
  
  /**
   * Récupération des commandes
   *
   * @param string $startDay
   * @param string $endDay
   * @return void
   */
  private function getOrders(string $startDay = null, string $endDay) {
    $orders = $this->orderRepository->findOrdersOnIntervalDay($startDay, $endDay);

    foreach($orders as $order) {
      $this->orders[] = $this->orderEntity($order);
    }    
  }

  /**
   * Création d'un nouveau OrderEntity
   *
   * @param array $order
   * @return void
   */
  private function orderEntity(array $order) {
    return new OrderEntity(
      $order['id'],
      $order['orderId'],
      $order['coverCode'],
      $order['model'],
      $order['family'],
      $order['orderFrom'],
      $order['orderBuyer'],
      $order['deliveredDate'],
      $order['quantity'],
      $order['partNumber'],
      $order['coverLink'],
      $order['orderDate'],
      $order['countryCode'],
      $order['countryName'],
      $order['wip'],
      $order['isValid'],
      $order['brand']
    );
  }
  
  /**
   * Renvoie une liste de date défini par 1 date de débu et une date de fin.
   *
   * @param string $startDay
   * @param string $endDay
   * @return void
   */
  private function setIntervalDayArray(string $startDay, string $endDay): void {
    $intervalDay = [];

    // Initialisation de la date
    $calculatedDate = $startDay;

    do {

      // Ajout date au tableau
      $intervalDay[] = [
        'date' => date('d-M-Y', strtotime($calculatedDate))
      ];
    
      //Actualisation de la date
      $calculatedDate = date('Y-m-d 00:00:00', strtotime($calculatedDate. '+1 day')); 
    }
    while(date('Y-m-d 00:00:00', strtotime($calculatedDate)) <= date('Y-m-d 00:00:00', strtotime($endDay)));        
    $this->intervalDays = $intervalDay;
  }

  /**
   * Undocumented function
   *
   * @return void
   */
  private function formatDashboardOrders() {
    foreach($this->orders as $order) {      

      // Vérification format de la données
      if(!$order instanceof OrderEntity) {
        throw new \Exception('Mauvais format de données');
      }

      if(!$this->isParNumberInDashboardArray($order->getPartNumber())) {
        
        // 
        $quantityByDateArray = $this->createQuantityByDateArray();        

        $this->iterateThroughOrders($order->getPartNumber(), $quantityByDateArray);       
       
        $dashboardOrderModel = $this->createDashboardOrderModel($order, $quantityByDateArray);
        $this->dashboardOrders[] = $dashboardOrderModel;
      }

      // var_dump($this->dashboardOrders);
    }
  }
}
