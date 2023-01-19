<?php
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/model/Order.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/exceptions/InvalidFormatException.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/interfaces/OrderRepositoryInterface.php';
require_once('/home/mdwfrkglvc/www/wp-config.php');

/**
 * Gestion requete SQL ORDER
 */
class MySqlOrderRepository implements OrderRepositoryInterface {

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
          'deliveredDate' => $order->getDeliveredDate(),
          'wip' => $order->getWip(),
          'coverLink'=> $order->getCoverLink(),
          'model'=>$order->getModel(),
          'isValid' => $order->getIsValid(),
          'brand' =>$order->getBrand()
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

        $query = "SELECT partNumber FROM orders WHERE partNumber = '" .$order->getPartNumber() ."' AND deliveredDate ='".$order->getdeliveredDate()."' AND wip <>'PREPARATION'";
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
   * Trouve les commandes dupliquées pour 1 partNumber
   *
   * @param string $partNumber - Le partNumber de la commande a vérifier
   * @param string $deliveredDate - Date de livraison
   * @return array - Commandes dupliquées
   */
  function findOneDuplicatedOrder(string $partNumber, string $deliveredDate): array {
    global $wpdb;
    $query = "SELECT partNumber FROM orders WHERE partNumber = '" .$partNumber."' AND deliveredDate ='".$deliveredDate."' AND wip <>'PREPARATION'";
    $findOrder = $wpdb->get_results($query, ARRAY_A);
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
  function findAllByOrderId(string $orderId): array
  { 
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
  function updateWip(string $wipValue, string $orderId): void {
    global $wpdb;
    $wpdb->query( $wpdb->prepare(
      "UPDATE orders SET wip = %s WHERE orderid = %s",
      $wipValue, $orderId
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
   * @return void
   */
  public function update(string $id, string $quantity, string $deliveredDate, string $status): void
  {
    global $wpdb;
    $order = $this->findOne($id);

    if(empty($order)) {
      throw new \Exception('Order Not Find');
    }

    $update = $wpdb->query( $wpdb->prepare(
      "UPDATE orders SET quantity=%s, deliveredDate=%s, wip = %s  WHERE id = %s",
      $quantity, $deliveredDate, $status, $id
      )
    );  

    var_dump($update);
    
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
    $wpdb->query($wpdb->prepare("DELETE FROM orders WHERE wip = 'PREPARATION' AND orderDate < '".$referenceDeleteDate."'"));
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
    $query = "SELECT * FROM orders WHERE wip <> 'PREPARATION' AND  deliveredDate >= '".$dayStart."' AND deliveredDate <= '".$dayEnd."'";
    $findOrder = $wpdb->get_results($query, ARRAY_A);
    return $findOrder;
  }
}