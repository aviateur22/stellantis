<?php
require_once('./stellantisOrder/services/MySqlOrderRepository.php');
require_once('./stellantisOrder/helpers/UpdateDashboardOrderHelper.php');
require_once('./stellantisOrder/utils/StaticData.php');


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

    // Instanciation des services et helpers
    $orderRepository = new MySqlOrderRepository();
    $updateDashboardOrder = new UpdateDashboardOrderHelper($orderRepository);

    // Mise a jour de la commande
    $updateDashboardOrder->updateOrder($orderId, $quantity, $deliveredDate, $status);  

    // Récupérationd de la commande mise a jour 
    $updatedOrder = $updateDashboardOrder->findUpdatedOrder($orderId);

    // Récupération de la nouveau nom de la class color
    $colorClassName = $updateDashboardOrder->findClassNameOrderColor($updatedOrder->wipId);
    $colorClassToRemove = $updateDashboardOrder->findColorClassToRemove();
    
    $data['updateOrder'] =  [
      'colorClassName' => $colorClassName,
      'colorClassToRemove' => $colorClassToRemove,
      'deliveredDate' =>date('d-M-Y', strtotime($updatedOrder->deliveredDate)),
      'quantity' => $updatedOrder->quantity
    ];

    echo(json_encode($data));
 } catch (\Throwable $th) {

    http_response_code($th->getCode());
    echo('Error ' . $th->getMessage());
 }