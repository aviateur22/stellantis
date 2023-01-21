<?php
require_once('./stellantisOrder/services/MySqlOrderRepository.php');
require_once('./stellantisOrder/helpers/DashboardOrderHelper.php');
require_once('./stellantisOrder/html/DisplayDashboardOrder.php');
require_once('./stellantisOrder/helpers/UserHelper.php');
require_once('./stellantisOrder/utils/validators.php');
error_reporting(E_ALL);

$partNumber = $_POST['partNumber'];
$startDate = $_POST['startDate'];
try {

if(empty($startDate)) {
  throw new \Exception('Update order impossible: Missing required order informations', 400);
}

$userHelper = new UserHelper();
$user = $userHelper->getUser();

$orderRepository = new MySqlOrderRepository();
$dashboardHelper = new DashboardHelper($orderRepository);

$test = $dashboardHelper->setDashboardOrders($startDate, '2023-02-25');

// Récupération des données
$dashboardOrders = $dashboardHelper->getDashboardOrders();
$intervalDays = $dashboardHelper->getIntervalDays();



// Creation html
$displayDashboardOrder = new DisplayDashboardOrder($dashboardOrders, $intervalDays, $user);


$data['orders'] = $displayDashboardOrder->createHtml();


echo(json_encode($data));

} catch(\Throwable $th) {
  http_response_code($th->getCode());
  echo('Error: ' .$th->getMessage());
}
