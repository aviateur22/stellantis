<?php
require_once('./stellantisOrder/services/XmlOrderFormat.php');
require_once('./stellantisOrder/services/FtpTransfert.php');
require_once('./stellantisOrder/services/MailService.php');
require_once('./stellantisOrder/helpers/ForecastPrintHelper.php');
require_once('./stellantisOrder/model/RepositoriesModel.php');
require_once('./stellantisOrder/utils/RepositorySelection.php');
require_once('./stellantisOrder/helpers/orderConfirmation/OrderFormConfirmationHelper.php');
require_once('./stellantisOrder/helpers/orderConfirmation/MessageOrderConfirmationHelper.php');

setlocale(LC_TIME, "fr_FR");

require('/home/mdwfrkglvc/www/wp-config.php');

error_reporting(E_ALL);

$orderId = $_POST['orderid'];
try {

  if(empty($orderId)) {
    throw new \Exception('Error : order ID is not valid', 400);
  }

  // Repository
  $repositorySelection = new RepositorySelection(StaticData::REPOSITORY_TYPE_MYSQL);
  $repositories = $repositorySelection->selectRepositories();

  // Activation des services + Helper
  $forecastPrintHelper = new ForecastPrintHelper($repositories);
  $xmlOrderFormat = new XmlOrderFormat($orderId, $repositories, $forecastPrintHelper);
  $ftpTransfert = new FtpTransfert($repositories, $orderId);
  $mailService = new MailService();
  $orderFormConfirmationHelper = new OrderFormConfirmationHelper(); 
  $messageOrderConfirmationHelper = new MessageOrderConfirmationHelper($orderId, $repositories, $mailService, $orderFormConfirmationHelper);

  // Vérification que toute les commandes valides
  $errorOrders = $repositories->getOrderRepository()->findErrorOrders($orderId);
  
  if(count($errorOrders) > 0) {
    throw new \Exception('Error order detected, file transfert cancel');
  }
  
  // Format les commandes a transférer
  $formatedPathOrders = $xmlOrderFormat->createFormatedOrders();

  // Préparation des messages pour les mails
  $messageOrderConfirmationHelper->prepareMessageForRequetedOrders();

  // Envois des emails
  $messageOrderConfirmationHelper->sendMessage();
  

  // Transfert Fichier
  $isTransfertSuccess = $ftpTransfert->transfertOrders($formatedPathOrders);

  if(!$isTransfertSuccess) {
    throw new \Exception('Error transferring oreder file, process cancel', 500);
  }  
 
  // Mise a jour du statut des commandes
  $ftpTransfert->updateOrderStatus();

  echo "Tranfert commandes OK";
}
catch(Throwable $th) {
  // Récupération code HTTP
  $statusCode = $th->getCode() === 0 ? 500 : $th->getCode();

  //Renvoie HTTP Response code
  http_response_code($statusCode);
  
  echo('Error ' . $th->getMessage());
}
