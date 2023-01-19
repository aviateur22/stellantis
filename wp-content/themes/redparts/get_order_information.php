<?php
require_once('./stellantisOrder/services/MySqlOrderRepository.php');

/**
 * Récupératon des données d'une commande
 */

 error_reporting(E_ALL);

 try {
 /**
  * Id de la commande
  */
  $orderId = $_POST['orderid'];

  if(!isset($orderId)) {
    throw new \Exception('Impossible de récupérer Id de la commande');
  }

  $orderRepository = new MySqlOrderRepository();

  // Recherche commande en base de données
  $order = $orderRepository->findOne($orderId);  

  if(count($order) === 0 ) {
    throw new \Exception('Commande non trouvée', 404);
  }

  
  $data['findOrder'] = $order;
  echo(json_encode($data));
 } catch (\Throwable $th) {
  echo('Error ' . $th->getMessage());
 }
 