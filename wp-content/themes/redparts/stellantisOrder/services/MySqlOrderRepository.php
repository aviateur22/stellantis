<?php
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/model/Order.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/exceptions/InvalidFormatException.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/interfaces/OrderRepositoryInterface.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/utils/StaticData.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/utils/validators.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/model/User.php';
require_once('/home/mdwfrkglvc/www/wp-config.php');

/**
 * Gestion requete SQL ORDER
 */
class MySqlOrderRepository implements OrderRepositoryInterface {

  /**
   * utilisateur connecté
   *
   * @var User
   */
  protected User $user;

  function __construct(User $user = null)
  {
    if($user) {
      $this->user = $user;
    }
    
    
  }
  /**
   * Sauvegarde de plusieurs commandes
   *
   * @param array $orders
   * @return void
   */
  function save(array $orders): void {  
    global $wpdb;  
    foreach($orders as $order) {
      // Verification instance Orer 
      if($order instanceof Order) {
        $wpdb->insert('orders', array(
          'orderId' => $order->getOrderId(),
          'orderDate' => $order->getOrderDate(),
          'orderFrom' => $order->getOrderform(),
          'orderBuyer' => $order->getOrderBuyer(),
          'family' => $order->getFamily(),
          'countryName' => $order->getCountryName(),
          'countryCode' => $order->getCountryCode(),
          'partNumber' => $order->getPartNumber(),
          'coverCode' => $order->getCoverCode(),
          'quantity' => $order->getQuantity(),
          'deliveredDate' => date('Y-m-d', strtotime($order->getDeliveredDate())),
          'coverLink'=> $order->getCoverLink(),
          'model'=>$order->getModel(),
          'isValid' => $order->getIsValid(),
          'brand' =>$order->getBrand(),
          'wipId' => $order->getWipId(),
          'version' => $order->getVersion(),
          'year' => $order->getYear(),
          'forecastPrint' =>$order->getPrintForecast()
        ));       
      } else {
        throw new InvalidFormatException();
      }     
    }   
  }

  /**
   * Renvoie les commandes dupliquées
   *
   * @param array $orders - Liste ds commandes 
   * @return array
   */
  function findDuplicatedOrder(array $orders): array {
    global $wpdb;

    // Array pour les partNumber dupliqués
    $duplicatedOrder = [];

    foreach($orders as $order) {
      if($order instanceof Order) {

        $query = "SELECT partNumber FROM orders WHERE partNumber = '" .$order->getPartNumber() ."' AND deliveredDate ='".$order->getdeliveredDate()."' AND wipId <>'".StaticData::ORDER_STATUS['PREPARATION']."'";
        $findOrder = $wpdb->get_results($query, ARRAY_A);
        
        if(count($findOrder) > 0) {
          if(!in_array($order->getPartNumber(), $duplicatedOrder)) {
            $duplicatedOrder[] = $order->getPartNumber();
          }           
        }
      }
    }

    return $duplicatedOrder;    
  }

  /**
   * Renvoie les commandes en erreur
   *
   * @param string $orderId
   * @return array
   */
  function findErrorOrders(string $orderId): array {
    global $wpdb;
    $findOrders = $wpdb->get_results(
      $wpdb->prepare(
        "SELECT * FROM orders WHERE orderId = %s AND isValid = 0",
          $orderId
        )
    );
    return $findOrders;
  }

    /**
   * Trouve les commandes dupliquées pour 1 partNumber
   *
   * @param string $partNumber - Le partNumber de la commande a vérifier
   * @param string $deliveredDate - Date de livraison
   * @param string $orderBuyer - Usine Stellantis qui a fait la commande
   * @return array - Commandes dupliquées
   */
  function findOneDuplicatedOrder(string $partNumber, string $deliveredDate, string $orderBuyer): array {
    global $wpdb;
    $findOrder = $wpdb->get_results(
      $wpdb->prepare(
        "SELECT partNumber FROM orders WHERE partNumber = %s AND deliveredDate = %s AND orderBuyer = %s AND wipId <> %s",
        $partNumber, $deliveredDate, $orderBuyer, StaticData::ORDER_STATUS['PREPARATION']
      ), ARRAY_A);
    return $findOrder;
  }

  /**
   * Recherche d'une commande a partir id de la commande
   *
   * @param string $orderId
   * @return array
   */
  function findOne(string $orderId): array {
    global $wpdb;
    $findOrder = $wpdb->get_results(
      $wpdb->prepare(
        "SELECT * FROM orders WHERE id = %s",
          $orderId
        )
    );
    return $findOrder;
  }

  /**
   * Suppression d'une commande
   *
   * @param string $partNumber
   * @param string $orderId
   * @param string $deliverdDate
   * @return void
   */
  function deleteOne(string $partNumber, string $orderId, string $deliveredDate): void {
    global $wpdb;    
    $wpdb->query(
      $wpdb->prepare(
        "DELETE FROM orders WHERE partNumber = %s AND orderId = %s AND deliveredDate = %s",
          $partNumber, $orderId, $deliveredDate
        )
    );    
  }

  /**
   * Récupère toutes les commandes pour un orderId
   *
   * @param string $orderId
   * @return array - Liste des commandes
   */
  function findAllByOrderId(string $orderId): array { 
    global $wpdb;
    $query = "SELECT * FROM orders WHERE orderId = '" .$orderId."'";
    $findOrders = $wpdb->get_results($query, ARRAY_A);

    return $findOrders;
  }

  /**
   * Modidfication des statuts d'une commande suivant le orderId
   *
   * @param string $wipValue
   * @param string $orderId
   * @return void
   */
  function updateWip(string $orderId): void {
    global $wpdb;
    $wpdb->query( $wpdb->prepare(
      
      "UPDATE orders SET wipId = IF(forecastPrint + quantity >= ".StaticData::MINIMUM_ORDER_QUANTITY_MANCHECOURT.", ".StaticData::ORDER_STATUS['BEFORE_PREFLIGHT_MA'].", ".StaticData::ORDER_STATUS['BEFORE_PREFLIGHT_MI'].") WHERE orderid = %s",
      $orderId
      )
    );    
  }

  /**
   * Mise a jour d'une commande
   *
   * @param string $orderId
   * @param string $quantity
   * @param string $deliveredDate
   * @param string $status
   * @return int
   */
  public function update(string $id, string $quantity, string $deliveredDate, string $status = null): int {
    global $wpdb;
    $order = $this->findOne($id);

    if(empty($order)) {
      throw new \Exception('Order Not Find', 400);
    }

    // Commande
    $existingOrder = $order[0];

    // Vérification si pas de conflit avec d'autre commande deja existante
    if(date('Y-m-d', strtotime($existingOrder->deliveredDate)) !== date('Y-m-d', strtotime($deliveredDate))) {
      
      $findDuplicatedOrder = $this->findOneDuplicatedOrder($existingOrder->partNumber, $deliveredDate, $existingOrder->orderBuyer);      
      if(!empty($findDuplicatedOrder)) {
        throw new \Exception('Update impossible, Order already exist on ' . date('d-M-Y', strtotime($deliveredDate)), 400);
      }
    }    

    // Si statut non défini
    if(empty($status)) {
      // Update commande
      $update = $wpdb->query( $wpdb->prepare(
        "UPDATE orders SET quantity=%s, deliveredDate=%s WHERE id = %s",
        $quantity, $deliveredDate, $id
        )
      );
    } else {
      // Update commande
      $update = $wpdb->query( $wpdb->prepare(
        "UPDATE orders SET quantity=%s, deliveredDate=%s, wipId = %s  WHERE id = %s",
        $quantity, $deliveredDate, $status, $id
        )
      );
    }
    return $update;
  }

  /**
   * Suppression des ancienne commandes non traité
   * 
   * @param string $referenceDeleteDate - Date avant laquelle toutes les commandes non traitées doivent être supprimées
   *
   * @return void
   */
  function deleteUnused(string $referenceDeleteDate) {
    global $wpdb;
    $wpdb->query($wpdb->prepare("DELETE FROM orders WHERE wipId = '".StaticData::ORDER_STATUS['PREPARATION']."' AND orderDate < '".$referenceDeleteDate."'"));
  }


   /**
   * Recherche toutes les commandes sur un certains nombre de jour 
   *
   * @param string $dayStart
   * @param string $dayEnd
   * @return array
   */
  function findOrdersOnIntervalDay(string $dayStart, string $dayEnd): array {
    global $wpdb;

    // Filtre les commandes pour les usines Stellantis   
    if(isUserRoleFindInArrayOfRoles($this->user, StaticData::FACTORY_STELLANTIS_ROLES_NAMES)) {
      $findOrders = $wpdb->get_results($wpdb->prepare("SELECT * FROM orders WHERE wipId <> %d AND deliveredDate >= %s AND deliveredDate <= %s AND orderBuyer = %s ORDER BY orderBuyer ASC, brand ASC, model ASC, `year` ASC, `version` ASC",
      StaticData::ORDER_STATUS['PREPARATION'], $dayStart, $dayEnd, $this->user->getFirstRole()), ARRAY_A);
      return $findOrders;      
    }

    // Autre personne de connectée
    $findOrders = $wpdb->get_results($wpdb->prepare("SELECT * FROM orders WHERE wipId <> %d AND deliveredDate >= %s AND deliveredDate <= %s ORDER BY orderBuyer ASC, brand ASC, model ASC, `year` ASC, `version` ASC",
    StaticData::ORDER_STATUS['PREPARATION'], $dayStart, $dayEnd), ARRAY_A);
    return $findOrders;
  }

  /**
   * Recherche toutes les commandes filtré par date + partNumber
   *
   * @param string $daySart
   * @param string $dayEnd
   * @param array $filterEntries
   * @return array
   */
  function findOrdersWithFilterPartNumber(string $dayStart, string $dayEnd, array $filterEntries): array {
    global $wpdb;

    // Recherche des PartNumbers
    $findOrderInPartNumber = [];
    if($filterEntries['partNumber']) {
      $partNumberQueryPlaceHolder = $this->getPlaceholder($filterEntries['partNumber'], '%s');
      $findOrderInPartNumber = $wpdb->get_results($wpdb->prepare("SELECT * FROM orders WHERE partNumber IN ($partNumberQueryPlaceHolder) ORDER BY orderBuyer ASC, brand ASC, model ASC, `year` ASC, `version` ASC",$filterEntries['partNumber']), ARRAY_A);
    }

    // Recherche des Orders dans l'interval de temps
    $findOrderInIntervallDay = $this->findOrdersOnIntervalDay($dayStart, $dayEnd);

    // Commandes validant les différents filtres
    $findOrders = [];

    foreach($findOrderInPartNumber as $orderInPartNumber) {
      foreach($findOrderInIntervallDay as $orderInIntervalDay) {
        if($orderInPartNumber === $orderInIntervalDay) {
          $findOrders[] = $orderInPartNumber;
        }
      }
    }
    return $findOrders;
  }

  /**
   * Placeholder statment pour une requete 
   *
   * @param array $queryArray
   * @param string $placeholder
   * @return string
   */
  private function getPlaceholder(array $queryArray, string $placeholder): string {
    $queryArrayCount = count($queryArray);
    $queryStringPlaceHolder = array_fill(0, $queryArrayCount, $placeholder);
    return implode(',', $queryStringPlaceHolder);
  }


  /**
   * Recherche de la première commande qui suit un date donnée
   *
   * @param string $specifiedDay
   * @return array
   */
  function findFirstOrderAfteSpecifiedDay(string $specifiedDay): array
  {
    global $wpdb;
    return $wpdb->get_results($wpdb->prepare("SELECT * FROM `orders` WHERE wipId <> %s AND deliveredDate > %s ORDER BY deliveredDate ASC LIMIT 1",
      StaticData::ORDER_STATUS['PREPARATION'], $specifiedDay), ARRAY_A);
  }
}