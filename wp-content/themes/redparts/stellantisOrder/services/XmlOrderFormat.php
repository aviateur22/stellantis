<?php
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/interfaces/OrderFormatInterface.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/interfaces/OrderRepositoryInterface.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/utils/OrderPdf.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/services/MySqlOrderRepository.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/exceptions/FolderNotFindException.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/model/FormatedOrder.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/helpers/ForecastPrintHelper.php';
require_once ('/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/model/RepositoriesModel.php');

/**
 * Format les Commandes pour transfert vers le client
 */
class XmlOrderFormat implements OrderFormatInterface {

  /**
   * Path pour sauvegarder les commandes générées
   */
  const SAVE_XML_PATH = '/home/mdwfrkglvc/www/wp-content/uploads/xml/';

  /**
   * OrdedrId
   *
   * @var string
   */
  protected string $orderId;

  /**
   * Repository
   *
   * @var OrderRepositoryInterface
   */
  protected OrderRepositoryInterface $orderRepository;

  /**
   * Calcul des prévisions de commandes
   *
   * @var ForecastPrintHelper
   */
  protected ForecastPrintHelper $forecastPrintHelper;

  function __construct(string $orderId, RepositoriesModel $repositories, ForecastPrintHelper $forecastPrintHelper) {
    $this->orderId = $orderId;
    $this->orderRepository = $repositories->getOrderRepository();
    $this->forecastPrintHelper = $forecastPrintHelper;
  }

  /**
   * Transforme la commande au format requis par le client
   *
   * @param array $orders - litste des commandes
   * @return array - Array de Object FormatedOrder
   */
  function createFormatedOrders(): array {
    // path des fichiers de créer
    $formatedOrders = [];

    // Récupération des commandes
    $orders = $this->getOrders();

    foreach($orders as $order) {
      // Création XML
      $formatedOrder = $this->createXml($order);      

      // Sauvegarde du XML
      $formatedOrders[] = $formatedOrder;
    }

    return $formatedOrders;
  }

  /**
   * Création du fichier XML
   *
   * @param array $order
   * @return FormatedOrder
   */
  private function createXml(array $order): FormatedOrder {

    // Récupération des type de docuementation PDF
    $orderPdf = new OrderPdf();
    
    
    // Parametrage XML
    $xmlFile = new DOMDocument('1.0', 'utf-8');
    $xmlFile->appendChild($bibliotheque = $xmlFile->createElement('bibliotheque'));

    // Propriété de la commande
    $bibliotheque->appendChild($livre = $xmlFile->createElement('order'));
    $livre->appendChild($xmlFile->createElement('oderId', $order['id']));
    $livre->appendChild($xmlFile->createElement('orderDate', $order['orderDate']));
    $livre->appendChild($xmlFile->createElement('orderFrom', $order['orderFrom']));
    $livre->appendChild($xmlFile->createElement('orderBuyer', $order['orderBuyer']));
    $livre->appendChild($xmlFile->createElement('countryName', $order['countryName']));
    $livre->appendChild($xmlFile->createElement('countryCode', $order['countryCode']));
    $livre->appendChild($xmlFile->createElement('partNumber', $order['partNumber']));
    $livre->appendChild($xmlFile->createElement('coverCode', $order['coverCode']));
    $livre->appendChild($xmlFile->createElement('quantity', $order['quantity']));
    $livre->appendChild($xmlFile->createElement('deliveredDate', $order['deliveredDate']));
    $livre->appendChild($xmlFile->createElement('coverLink', $order['coverLink']));
    $livre->appendChild($xmlFile->createElement('family', $order['family']));
    $livre->appendChild($xmlFile->createElement('model', $order['model']));
    $livre->appendChild($xmlFile->createElement('orderForecastPrevisions', $order['forecastPrint']));
    
    // Ajout des liens PDF des manuels
    $pdfDocumentations = $orderPdf->getPdfLink($order);
    foreach($pdfDocumentations as $pdfLink) {
      $livre->appendChild($xmlFile->createElement("documentation" ,$pdfLink .' - ' . $order['quantity']));
    }

    // Format + Sauvegarde
    $xmlFile->formatOutput = true;

    // Dossier de sauvegarde inexistant
    if(!file_exists(self::SAVE_XML_PATH)) {
      throw new FolderNotFindException();
    }

    // Fichier XML vide
    if(!isset($xmlFile)) {
      throw new \Exception(' Erreur dans la création du XML');
    }

    // Path de sauvegarde du fichier
    $savePath = self::SAVE_XML_PATH.$order['id'].'.xml';


    // Sauvegarde
    $xmlFile->save($savePath);

    // FormatedOrder
    $destinationFileName = $order['partNumber'].'-'.$order['deliveredDate'];
    $orderQuantityClacultated = (int)$order['quantity'] + (int)$order['forecastPrint'];
    $formatedOrder = new FormatedOrder($savePath, $orderQuantityClacultated, $destinationFileName);
    return $formatedOrder;
  }
  
  /**
   * Renvoie les Commandes raataché a un orderId
   *
   * @return array
   */
  private function getOrders(): array {
    $orders = $this->orderRepository->findAllByOrderId($this->orderId);
    return $orders;
  }
}