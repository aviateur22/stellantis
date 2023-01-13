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


    // Format les commandes a transférer
    $formatedPathOrders = $xmlOrderFormat->createFormatedOrders();


    


    var_dump('ICI');
    echo "OK";
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
