<?php
require_once('./stellantisOrder/helpers/DashboardOrderHelper.php');
require_once('./stellantisOrder/html/DisplayDashboardOrder.php');
require_once('./stellantisOrder/helpers/DisplayOrderColorHelper.php');
require_once('./stellantisOrder/helpers/UserHelper.php');
require_once('./stellantisOrder/utils/validators.php');
require_once('./stellantisOrder/helpers/html/UpdateOrderModalHelper.php');
require_once('./stellantisOrder/model/RepositoriesModel.php');
require_once('./stellantisOrder/utils/RepositorySelection.php');
error_reporting(E_ALL);

$partNumber = $_POST['partNumber'];
$startDate = $_POST['startDate'];
try {

if(empty($startDate)) {
  throw new \Exception('Update order impossible: Missing required order informations', 400);
}

// Vérification format de la date
if(!isDateValid($startDate)){
  throw new \Exception('Unvalid date and time', 400);
}

// Données provenant du filtre de orderDashboard
$filterEntries = [];

// Ajout des PartNumber dans le tableau des filtres
if(!empty($partNumber)) {
  $filterEntries['partNumber'] = $partNumber;
}

$userHelper = new UserHelper();
$user = $userHelper->getUser();

// Repositories
$repositorySelection = new RepositorySelection(StaticData::REPOSITORY_TYPE_MYSQL);
$repositories = $repositorySelection->selectRepositories($user);

// Helper + Services
$displayOrderColorHelper = new DisplayOrderColorHelper($user);
$dashboardHelper = new DashboardHelper($repositories);
$setDashboard = $dashboardHelper->setDashboardOrders($startDate, '2023-02-25', $filterEntries);

// Récupération des données
$dashboardOrders = $dashboardHelper->getDashboardOrders();
$intervalDays = $dashboardHelper->getIntervalDays();

// Creation html
$updateOrderModalHelper = new UpdateOrderModalHelper($user);
$displayDashboardOrder = new DisplayDashboardOrder(
  $dashboardOrders, 
  $intervalDays,  
  $user, 
  $displayOrderColorHelper,
  $updateOrderModalHelper
);


$data['orders'] = $displayDashboardOrder->createHtml();


echo(json_encode($data));

} catch(\Throwable $th) {
  // Récupération code HTTP
  $statusCode = $th->getCode() === 0 ? 500 : $th->getCode();

  //Renvoie HTTP Response code
  http_response_code($statusCode);
  
  echo('Error ' . $th->getMessage());
}
