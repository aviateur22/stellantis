<?php
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/utils/validators.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/utils/StaticData.php';
require_once ('/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/model/Order.php');
require_once ('/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/model/DashboardOrderModel.php');
require_once ('/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/model/OrderEntity.php');
require_once ('/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/helpers/CreateDashboardOrdersHelper.php');
require_once ('/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/model/RepositoriesModel.php');



class DashboardOrderHelper extends CreateDashboardOrdersHelper {

  /**
   * Order repository
   *
   * @var OrderRepositoryInterface
   */
  protected OrderRepositoryInterface $orderRepository;

  function __construct(RepositoriesModel $repositories)
  {
    $this->orderRepository = $repositories->getOrderRepository();
  }

  /**
   * Undocumented function
   *
   * @param string|null $startDay
   * @param string $endDay
   * @param array $filterEntries - données du filtre orderDashboard
   * @return void
   */
  public function setDashboardOrders(string $startDay = null, string $endDay, array $filterEntries = null) {
    // Formate les dates de début et de fin
    $this->formatDayInterval($startDay, $endDay);

    // Récupéaration de la liste des dates de l'interval 
    $this->setIntervalDayArray($startDay, $endDay);

    // Récupéaration des commandes en base de donnée
    if(empty($filterEntries)) {
      $this->getOrders($startDay, $endDay);
    } else {
      $this->getFilterOrders($startDay, $endDay, $filterEntries);
    }
    

    // Récupération
    $this->formatDashboardOrders();    
  }

  /**
   * Recherche de la 1ere commande qui suit une date donnée
   *
   * @param  string $afterDate - Date a partir de laquelle on cherche une commande
   * @return string
   */
  public function findFirstDayWithOrder(string $afterDate = null, $endDay = null): string {
    // Formate les dates de début et de fin
    $this->formatDayInterval($afterDate, $endDay);

    // Recherche de la 1ere commande
    $findOrder = $this->orderRepository->findFirstOrderAfteSpecifiedDay($afterDate);

    if(!count($findOrder) > 0) {
      return '';
    }
    
    return $findOrder['deliveredDate'];
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
   * Formate les dates
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
    $endDay = date('Y-m-d 00:00:00', strtotime('+'.(StaticData::DASHBOARD_INTERVAL_DAY - 1).' day', strtotime($startDay)));
  }

  /**
   * Récupération des commandes filtrées
   *
   * @param string|null $startDay
   * @param string $endDay
   * @param string $partNumber
   * @return void
   */
  private function getfilterOrders(string $startDay = null, string $endDay, array $filterEntries) {    
    // 
    $partNumberInArray = $this->stringToArray($filterEntries['partNumber'], ',');
    
    // Données du filtre pour la requete en Base de données
    $prepareFilterEntries = [
      'partNumber' => $partNumberInArray,
    ];

    $orders = $this->orderRepository->findOrdersWithFilterPartNumber($startDay, $endDay, $prepareFilterEntries);
    
    foreach($orders as $order) {
      $this->orders[] = $this->orderEntity($order);
    }  
  }

  /**
   * Renvoie une chaine de string en array
   *
   * @param string $stringFormat
   * @param string $separator
   * @return array
   */
  private function stringToArray(string $stringFormat, string $separator): array {
    // Néttoyage des données text
    $stringFormat = trim($stringFormat, $separator);
    $stringFormat = trim($stringFormat);

    // Transforme les données du filtre en Array
    $arrayFormat = explode($separator, $stringFormat);

    // Néttoyage de chaque élément du tableau
    for($i = 0; $i < count($arrayFormat); $i++) {
      $arrayFormat[$i] = trim($arrayFormat[$i]);
    }

    return $arrayFormat;
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
      $order['wipId'],
      $order['isValid'],
      $order['brand'],
      $order['version'],
      $order['year'],
      $order['forecastPrint'],
      $order['documentationPDFInformations']
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
   * Regroupement des commandes par :
   * - PartNumber
   * - OrderBuyer
   * 
   *
   * @return void
   */
  private function formatDashboardOrders() {
    
    foreach($this->orders as $order) {      

      // Vérification format de la données
      if(!$order instanceof OrderEntity) {
        throw new \Exception('Error Instanceof model - DashboardOrdersHelper', 500);
      }

      // si commande non presente
      if(!$this->isParNumberInDashboardArray($order->getPartNumber(), $order->getOrderBuyer())) {
        
        // 
        $quantityByDateArray = $this->createQuantityByDateArray();        

        $this->iterateThroughOrders($order->getPartNumber(), $quantityByDateArray, $order->getOrderBuyer());       
       
        $dashboardOrderModel = $this->createDashboardOrderModel($order, $quantityByDateArray);
        $this->dashboardOrders[] = $dashboardOrderModel;
      }      
    }
  }
}
