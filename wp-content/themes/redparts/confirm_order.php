<?php
require_once('./stellantisOrder/services/MySqlOrderRepository.php');
require_once('./stellantisOrder/services/MySqlForecastRepository.php');
require_once('./stellantisOrder/services/XmlOrderFormat.php');
require_once('./stellantisOrder/services/FtpTransfert.php');
require_once('./stellantisOrder/helpers/ForecastPrintHelper.php');

setlocale(LC_TIME, "fr_FR");

require('/home/mdwfrkglvc/www/wp-config.php');

error_reporting(E_ALL);

$orderId = $_POST['orderid'];

if(isset($orderId)) {
  try{
    // Activation des services
    $orderRepository = new MySqlOrderRepository();
    $forecastRepository = new MySqlForecastRepository();
    $forecastPrintHelper = new ForecastPrintHelper($forecastRepository);
    $xmlOrderFormat = new XmlOrderFormat($orderId, $orderRepository, $forecastPrintHelper);
    $ftpTransfert = new FtpTransfert($orderRepository, $orderId);

    // Vérification que toute les commandes valides
    $errorOrders = $orderRepository->findErrorOrders($orderId);
    
    if(count($errorOrders) > 0) {
      throw new \Exception('Error order detected, file transfert cancel');
    }
    
    // Format les commandes a transférer
    $formatedPathOrders = $xmlOrderFormat->createFormatedOrders();    

    // Transfert Fichier
    $ftpTransfert->transfertOrders($formatedPathOrders);

    // Mise a jour du statut des commandes
    $ftpTransfert->updateOrderStatus();  

    echo "Tranfert commandes OK";
  }
  catch(Throwable $th) {
    echo($th->getMessage());
  }  
}
else {
  echo "Error : order ID is not valid";
  return;
}
?>
