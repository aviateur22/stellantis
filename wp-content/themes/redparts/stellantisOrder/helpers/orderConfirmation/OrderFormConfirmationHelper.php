<?php
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/utils/OrderPdf.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/interfaces/OrderRepositoryInterface.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/interfaces/MailServiceInterface.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/model/RepositoriesModel.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/services/MySqlOrderRepository.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/xlsxOrderCreated.php';

/**
 * Helper - Génération Bon de commande fichier XLS
 */
class OrderFormConfirmationHelper {

  /**
   * Nom des fichiers XLS pour Millau
   *
   * @var array
   */
  protected array $xlsFileForMillau = [];

  /**
   * Nom des fichiers XLS pour Manchecourt
   *
   * @var array
   */
  protected array $xlsFileForManchecourt = [];

  /**
   * Formation d'un tableau pour la génération de fichier XLS
   *
   * @return void
   */
  function dispacthOrderBetweenMiAndMA(array $orders) {    

    // Liste des commanndes pour Millau et Manchecourt
    $ordersForMillau = [];
    $ordersForManchecourt = [];

    // Tri les commande entre Millau et Manchecourt
    foreach($orders as $order) {

      $this->isOrderMessageForMillau($order) ?
        $ordersForMillau[] = $order : 
        $ordersForManchecourt[] = $order;
    }

   
    // Sort MI and MA Array par date
    usort($ordersForMillau, fn ($a, $b) => strcmp(date('Y-m-d', strtotime($a['deliveredDate'])), date('Y-m-d', strtotime($b['deliveredDate']))));
    usort($ordersForManchecourt, fn ($a, $b) => strcmp(date('Y-m-d', strtotime($a['deliveredDate'])), date('Y-m-d', strtotime($b['deliveredDate']))));    

    // Regroupement des commandes par deliveredDate et génération d'un XLS
    $this->iterateThroughtOrders($ordersForMillau, StaticData::MILLAU_FACTORY_NAME);
    $this->iterateThroughtOrders($ordersForManchecourt, StaticData::MANCHECOURT_FACTORY_NAME);
  }

  /**
   * Boucle sur la liste des commandes de Millau ou Manchecourt
   *
   * @param array $orders - Liste des commandes Millau ou Manchecourt
   * @param string $mauryFactoryName - Nom de l'usine
   * @return void
   */
  function iterateThroughtOrders(array $orders, $mauryFactoryName) {
    $deliveredDate = '';
    for($i = 0; $i < count($orders); $i++) {
      if(date('Y-m-d', strtotime($deliveredDate)) !== date('Y-m-d', strtotime($orders[$i]['deliveredDate']))) {
        $deliveredDate = $orders[$i]['deliveredDate'];

        // Génération des XLS et sauvegarde des nom de fichier
        $mauryFactoryName === StaticData::MILLAU_FACTORY_NAME ? 
          $this->xlsFileForMillau[] = $this->filterOrderByDateAndGenerateXlsFile($orders, $orders[$i]['deliveredDate']) :
          $this->xlsFileForManchecourt[] = $this->filterOrderByDateAndGenerateXlsFile($orders, $orders[$i]['deliveredDate']);
      }      
    }
  }

  /**
   * Dispactch des élément des commandes
   *
   * @param array $orders - Liste des commandes (Millau ou Manchecourt)
   * @param string $deliveredDate - Date de filtre des commandes
   * @return string
   */
  function filterOrderByDateAndGenerateXlsFile(array $orders, string $deliveredDate): string {

    $sameDeliveredDateOrders = [];
    $docType ='docType';

    foreach($orders as $order) {
      if(date('Y-m-d', strtotime($order['deliveredDate'])) === date('Y-m-d', strtotime($deliveredDate))) {
        $sameDeliveredDateOrders[]= $order;
      }
    }

    // Parcours des commandes ayant une meme deliveredDate
    foreach($sameDeliveredDateOrders as $key=>$sameDeliveredDateOrder) {
      $orderPdf = new OrderPdf();
      $fileNames = $orderPdf->getPdfLinkForXls($sameDeliveredDateOrder);

      // Création des données
      $xlsOrderArray[$key]['partNumber'] = $sameDeliveredDateOrder['partNumber'];
      $xlsOrderArray[$key]['quantity'] = $sameDeliveredDateOrder['quantity'];

      foreach($fileNames as $keyName=>$fileName) {
        $xlsOrderArray[$key]['fileName'.$keyName] = $fileName['fileName'];
        $docType = $fileName['fileType'] !== 'undefined' ? $fileName['fileType'] : 'docType';
      }
    }
    
    // Génration XLS
    return $this->createXls($docType, date('Y-m-d'), $deliveredDate, $xlsOrderArray);
  }

  /**
   * Génération d'un fichier XLS
   *
   * @param string $deliveredDate
   * @param array $xlsOrderArray
   * @param string $docType
   * @param string $orderDate
   * @return string - Nom du fichier XLS
   */
  function createXls($docType, $orderDate, $deliveredDate, $xlsOrderArray): string {   
    if(count($xlsOrderArray) === 0) {
      return '';
    }

    // Génération d'un fichhier XLS est sauvegarde du path
    return CreateXlsOrder($docType, $orderDate, $deliveredDate, $xlsOrderArray);
  }

  /**
   * Renvoie les path des Bon de commandes de MI
   *
   * @return array
   */
  function getPathOfMillauOrderForm(): array {
    return $this->xlsFileForMillau;
  }

  /**
   * Renvoie les path des Bon de commandes de MA
   *
   * @return array
   */
  function getPathOfManchecourtOrderForm(): array {
    return $this->xlsFileForManchecourt;
  }


  /**
   * Vérifie si commande pour MI ou MA (Quntity + Forecast > 2000)
   *
   * @return bool
   */
  protected function isOrderMessageForMillau(array $order): bool {
    return (int)$order['quantity'] + (int)$order['forecastPrint'] < StaticData::MINIMUM_ORDER_QUANTITY_MANCHECOURT;
  }

}