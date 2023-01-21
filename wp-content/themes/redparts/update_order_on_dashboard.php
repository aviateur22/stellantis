<?php
require_once('./stellantisOrder/services/MySqlOrderRepository.php');

/**
 * Update des données d'une commande
 */

 error_reporting(E_ALL);

 try {
 /**
  * Id de la commande
  */
  $orderId = $_POST['id'];
  $quantity = $_POST['quantity'];
  $deliveredDate = $_POST['deliveredDate'];
  $status = $_POST['status'];

  if(empty($orderId) || empty($quantity) || empty($deliveredDate) || empty($status)) {
    throw new \Exception('Update impossible: Missing required order informations', 400);
  }

  $orderRepository = new MySqlOrderRepository();

  // Recherche commande en base de données
  $orderRepository->update($orderId, $quantity, $deliveredDate, $status);
  
  $data['updateOrder'] = 'update-success';

  echo(json_encode($data));
 } catch (\Throwable $th) { 
  http_response_code($th->getCode());
  echo('Error ' . $th->getMessage());
 }