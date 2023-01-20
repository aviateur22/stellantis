<?php
require_once('./stellantisOrder/services/MySqlOrderRepository.php');

$partNumber = $_POST['partnumber'];
$orderId = $_POST['orderid'];
$deliveredDate = $_POST['orderdate'];

/**
 * Supprression d'une commande
 */
if(isset($partNumber) && isset($orderId) && isset($deliveredDate)){
 
  // Repository
  $mySqlOrderRepository = new MySqlOrderRepository(); 
  $mySqlOrderRepository->deleteOne($partNumber, $orderId, $deliveredDate);
  
  // Renvoie des données 
  $data['delete-result'] = true;
  echo(json_encode($data));

} else {
  // Renvoie des données 
  $data['delete-result'] = 'error on delete';
  echo(json_encode($data));
}