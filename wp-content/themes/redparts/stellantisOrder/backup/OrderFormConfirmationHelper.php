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

   
    // Classement des commandes par deliveredDate
    usort($ordersForMillau, fn ($a, $b) => strcmp(date('Y-m-d', strtotime($a['deliveredDate'])), date('Y-m-d', strtotime($b['deliveredDate']))));
    usort($ordersForManchecourt, fn ($a, $b) => strcmp(date('Y-m-d', strtotime($a['deliveredDate'])), date('Y-m-d', strtotime($b['deliveredDate']))));    

    // Boucle sur les commandes de Millau et Macnchecourt
    $this->iterateThroughtOrders($ordersForMillau, StaticData::MILLAU_FACTORY_NAME);
    $this->iterateThroughtOrders($ordersForManchecourt, StaticData::MANCHECOURT_FACTORY_NAME);

    var_dump('xlsFileForManchecourt');
    var_dump($this->xlsFileForManchecourt);

    var_dump('xlsFileForMillau');
    var_dump($this->xlsFileForMillau);
  }

  /**
   * Boucle sur la liste des commandes de Millau ou Manchecourt
   *
   * @param array $orders - Liste des commandes Millau ou Manchecourt
   * @param string $mauryFactoryName - Nom de l'usine
   * @return void
   */
  function iterateThroughtOrders(array $orders, $mauryFactoryName): void {
    $deliveredDate = '';
    for($i = 0; $i < count($orders); $i++) {
      if(date('Y-m-d', strtotime($deliveredDate)) !== date('Y-m-d', strtotime($orders[$i]['deliveredDate']))) {
        $deliveredDate = $orders[$i]['deliveredDate'];

        //Filtre les commandes par date et type de commande et génération d'un fichier XLS
        $this->filterOrderByDateAndTypeAndGenerateXlsFile($orders, $orders[$i]['deliveredDate'], $mauryFactoryName);
      }      
    }
  }

  /**
   * Regrouprement des commandes par deliveredDate et docType puis génération d'un fichier XLS (Bon de commande)
   *
   * @param array $orders - Liste des commandes (Millau ou Manchecourt)
   * @param string $deliveredDate - Date de filtre des commandes
   * @param string $mauryFactoryName - Nom de l'usine
   * @return void
   */
  function filterOrderByDateAndTypeAndGenerateXlsFile(array $orders, string $deliveredDate, string $mauryFactoryName): void {

    // Liste des commandes avec la même deliveredDate
    $sameDeliveredDateOrders = [];

    // Liste des differents docTypes présent dans la liste des commandes
    $doctTypeInDeliveredDateOrders = [];

    
    // Recherche des commandes avec une même deliveredDate
    foreach($orders as $order) {
      if(date('Y-m-d', strtotime($order['deliveredDate'])) === date('Y-m-d', strtotime($deliveredDate))) {
        $sameDeliveredDateOrders[] = $order;

        // Récupération des DocType de la commande
        $docTypesOfOrder = OrderPdf::returnAllDocTypeOfOneOrder($order);

        // Ajout des nouveau doctype dans la liste
        $this->addDocTypeIfNoteInArray($docTypesOfOrder, $doctTypeInDeliveredDateOrders);
      }
    }

    // Génération des fichiers Bon de commande en fonction du Type
    foreach($doctTypeInDeliveredDateOrders as $docType) {
      foreach($sameDeliveredDateOrders as $key=>$sameDeliveredDateOrder) {
        $fileNames = OrderPdf::getPdfLinkForXls($sameDeliveredDateOrder);
        foreach($fileNames as $fileName) {
          if($docType === $fileName['type']) {
            // Création des données
            $xlsOrderArray[$key]['partNumber'] = $sameDeliveredDateOrder['partNumber'];
            $xlsOrderArray[$key]['quantity'] = $sameDeliveredDateOrder['quantity'];
            $xlsOrderArray[$key]['fileName'] = $fileName['docRef'];
          }
        }        
      }

      // Génration XLS
      $orderFormFileName = $this->createXls($docType, date('Y-m-d'), $deliveredDate, $xlsOrderArray);

      // Ajout du nom du fichier dans la liste
      $mauryFactoryName === StaticData::MILLAU_FACTORY_NAME ? 
          $this->xlsFileForMillau[] = $orderFormFileName :
          $this->xlsFileForManchecourt[] = $orderFormFileName;
      
          var_dump($xlsOrderArray);
          var_dump('Fin xlsArray Nom du DocType: ' .  $docType . ' et deliveredDate : ' . $deliveredDate);
      // Reset
      $xlsOrderArray = [];
    }
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

  /**
   * Vérification si un ou plusieurs docTyp sont présent dans la liste 
   *
   * @param array $newDocTypes
   * @param array $availableDocTypes
   * @return void
   */
  protected function addDocTypeIfNoteInArray(array $newDocTypes, array &$availableDocTypes): void {
    
    foreach($newDocTypes as $docType) {
      if(!in_array( $docType, $availableDocTypes)) {
        $availableDocTypes[] =  $docType;
      }
    }
  } 

}