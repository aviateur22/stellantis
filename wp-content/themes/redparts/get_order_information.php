<?php
require_once('./stellantisOrder/services/MySqlOrderRepository.php');
require_once('./stellantisOrder/helpers/FindOrderInformationHelper.php');
require_once('./stellantisOrder/helpers/UserHelper.php');

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
    throw new \Exception('Impossible de récupérer Id de la commande', 400);
  }

  // User
  $userHelper = new UserHelper();
  $user = $userHelper->getUser();

  // Service + helper
  $displayOrderColorHelper = new DisplayOrderColorHelper($user);
  $orderRepository = new MySqlOrderRepository();
  $findOrderInformationHelper = new FindOrderInformationHelper($orderRepository, $displayOrderColorHelper);

  // Recherche Commande
  $order = $findOrderInformationHelper->findOrder($orderId);

  // Recherche de l'usine ayant la commande
  $processedWith = $findOrderInformationHelper->findProcessedWith((int)$order['wipId']);

  $orderStatusName = $findOrderInformationHelper->getOrderStatusLabel((int)$order['wipId']);

  $orderClassName = $findOrderInformationHelper->getOrderstatusColorClassName((int)$order['wipId']);

  
  $data['findOrder'] = [
    'order' => $order,
    'processedWith' => $processedWith,
    'orderStatus' => $orderStatusName,
    'orderColorClassName' => $orderClassName
  ];
  
  echo(json_encode($data));

} catch (\Throwable $th) {
  // Récupération code HTTP
  $statusCode = $th->getCode() === 0 ? 500 : $th->getCode();

  //Renvoie HTTP Response code
  http_response_code($statusCode);
  
  echo('Error ' . $th->getMessage());
 }
 