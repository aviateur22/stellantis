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

  if(!isset($orderId) || !isset($quantity) || !isset($deliveredDate) || !isset($status)) {
    throw new \Exception('Impossible de récupérer Id de la commande');
  }

  $orderRepository = new MySqlOrderRepository();

  // Recherche commande en base de données
  $orderRepository->update($orderId, $quantity, $deliveredDate, $status);
  
  $data['updateOrder'] = 'update-success';

  echo(json_encode($data));
 } catch (\Throwable $th) {
  echo('Error ' . $th->getMessage());
 }