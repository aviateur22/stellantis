<?php
require_once('./stellantisOrder/services/MySqlOrderRepository.php');

$partNumber = $_POST['partnumber'];
$orderId = $_POST['orderid'];
$deliveredDate = $_POST['orderdate'];

/**
 * Supprression d'une commande
 */
try {  
  if(empty($partNumber) || empty($orderId) && empty($deliveredDate)) {
    throw new \Exception('Delete order impossible: Missing required order informations', 400);
  }

  // Repository
  $mySqlOrderRepository = new MySqlOrderRepository(); 
  $mySqlOrderRepository->deleteOne($partNumber, $orderId, $deliveredDate);
  
  // Renvoie des données 
  $data['delete-result'] = true;
  echo(json_encode($data));
}
catch (Throwable $th) {
  http_response_code($th->getCode());
  echo('Error ' . $th->getMessage());
}