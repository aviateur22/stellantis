<?php
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/utils/OrderPdf.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/interfaces/OrderRepositoryInterface.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/interfaces/MailServiceInterface.php';
require_once ('/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/model/RepositoriesModel.php');
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/services/MySqlOrderRepository.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/xlsxOrderCreated.php';
/**
 * Helper envoie conformation de commande
 */
class OrderMessageConfirmationHelper {

  /**
   * Repository
   *
   * @var OrderRepositoryInterface
   */
  protected OrderRepositoryInterface $orderRepository;

  /**
   * MailService
   *
   * @var MailServiceInterface
   */
  protected MailServiceInterface $mailService;

  /**
   * Id des commandes
   *
   * @var string
   */
  protected string $orderId;

  /**
   * Liste des commandes
   *
   * @var array
   */
  protected array $orders;

  /**
   * Liste des messages a envoyer
   *
   * @var array
   */
  protected array $orderMessages;

  function __construct(string $orderId, RepositoriesModel $repositories, MailServiceInterface $mailService) {
    $this->orderRepository = $repositories->getOrderRepository();
    $this->mailService = $mailService;
    $this->orderId = $orderId;
    
    $this->getOrders();
  }

  /**
   * Préparation d'une liste de message
   *
   * @return void
   */
  function prepareMessageForRequetedOrders() {

    // Initilasation des messages
    $messageForMillau = '';
    $messageForManchecourt = '';

    // Index
    $orderQuantityMillaux = 1;
    $orderQuantityManchecourt = 1;

    // Boucle sur les commandes
    foreach($this->orders as $order) {

      // Si messahe pour Millau
      if($this->isOrderMessageForMillau($order)) {

        $messageForMillau .= $this->formatMessage($order, $orderQuantityMillaux);
        $orderQuantityMillaux ++;

      } else {

        $messageForManchecourt .= $this->formatMessage($order, $orderQuantityManchecourt);
        $orderQuantityManchecourt++;

      }
    }

    // Stocke les messages pour envoient
    $this->orderMessages = [
      [
        'to' => StaticData::MANCHECOURT_FACTORY_NAME,
        'message'=> $this->initMessage(). $messageForManchecourt . $this->endMessage()
      ],
      [
        'to' => StaticData::MILLAU_FACTORY_NAME,
        'message' => $this->initMessage(). $messageForMillau . $this->endMessage()
      ]
    ];
    
    // Création des fichir XLS
    $this->arrayOfOrdersForXlsFileCreation();
  }

  /**
   * Envoie des emails de commandes
   *
   * @return void
   */
  function sendMessage() {

    foreach($this->orderMessages as $orderMessage) {
      $this->mailService->sendMessage('aviateur22@hotmail.fr', 'new order', $orderMessage['message']);
    }
  }
  /**
   * Vérifie si commande pour MI ou MA (Quntity + Forecast > 2000)
   *
   * @return bool
   */
  function isOrderMessageForMillau(array $order) {
    return (int)$order['quantity'] + (int)$order['forecastPrint'] < StaticData::MINIMUM_ORDER_QUANTITY_MANCHECOURT;
  }

  /**
   * Message résumé d'une commande
   *
   * @param array $order
   * @return string
   */
  function formatMessage(array $order, int $index): string {

    $orderMessage = '';
    $orderMessage .= 'Record N°: ' . $index .'\r\n';
    $orderMessage .= 'PartNumber: ' . $order['partNumber'] .'\r\n';
    $orderMessage .= 'Brand: ' . $order['brand'] .'/'. $order['model'] .'/'. $order['year'] .'/'. $order['verion'] .'\r\n';
    $orderMessage .= 'Quantity: ' .$order['quantity'].'\r\n';
    $orderMessage .= 'Forecast quantity: ' .$order['forecastPrint'].'\r\n';
    $orderMessage .= 'Delivered date: ' .$order['deliveredDate'].'\r\n';
    $orderMessage .= $this->formatPdfLinkMessage($order) .'\r\n' .'\r\n';

    var_dump($orderMessage);
    return $orderMessage;
    
  }

  /**
   * Début du message
   *
   * @return void
   */
  function initMessage() {
    return 'Bonjour, 

    Une nouvelle commande vient d’être déposée sur la plateforme STELLANTIS'.'\r\n'.'\r\n';
  }

  /**
   * Fin du message
   *
   * @return void
   */
  function endMessage() {
    return 'Mail diffusé automatiquement par la plateforme STELLANTIS. Ne pas répondre.';
  }

  /**
   * Format les liens des PDF
   *
   * @param array $order
   * @return string
   */
  function formatPdfLinkMessage(array $order): string {
    // Récupération des types de documentation PDF
    $orderPdf = new OrderPdf();

    // Initialisation 
    $pdfTextMessage = '';

    // Boucle sur les données des PDF
    $pdfDocumentations = $orderPdf->getPdfLinkForMail($order);
    foreach($pdfDocumentations as $pdfLink) {
      $pdfTextMessage .= $pdfLink . ':  Quantity ='.$order['quantity']. '\r\n';
    }

    return $pdfTextMessage;
  }

  /**
   * Renvoie les Commandes rattaché a un orderId
   *
   * @return void
   */
  private function getOrders(): void {
    $this->orders = $this->orderRepository->findAllByOrderId($this->orderId);
  }

  /**
   * Formation d'un tableau pour la génération de fichier XLS
   *
   * @return void
   */
  private function arrayOfOrdersForXlsFileCreation() {    

    // Liste des commanndes pour Millau et Manchecourt
    $ordersForMillau = [];
    $ordersForManchecourt = [];

    foreach($this->orders as $key=>$order) {

      $this->isOrderMessageForMillau($order) ?
        $ordersForMillau[] = $order : 
        $ordersForManchecourt[] = $order;
    }

    // Sort MI and MA Array
    usort($ordersForMillau, fn ($a, $b) => strcmp(date('Y-m-d', strtotime($a['deliveredDate'])), date('Y-m-d', strtotime($b['deliveredDate']))));
    usort($ordersForManchecourt, fn ($a, $b) => strcmp(date('Y-m-d', strtotime($a['deliveredDate'])), date('Y-m-d', strtotime($b['deliveredDate']))));

    $orderForMillauDispatchByDeliveredDate = [
      'to' => '',
      'data' => [        
      ]
    ];

    $orderForManchecourtDispatchByDeliveredDate = [
      'to' => '',
      'data' => [        
      ]
    ];

    // Tri par date
    $this->dispacthOrderInNewArray($ordersForMillau, $orderForMillauDispatchByDeliveredDate);
    $this->dispacthOrderInNewArray($ordersForManchecourt, $orderForManchecourtDispatchByDeliveredDate);

    var_dump($orderForManchecourtDispatchByDeliveredDate, $orderForMillauDispatchByDeliveredDate);
  }

  /**
   * Undocumented function
   *
   * @param array $orders - Liste des commande a dispacther
   * @param [type] $dispatchResult - Stock les résulatat du dispatch
   * @return void
   */
  function dispacthOrderInNewArray(array $orders, &$dispatchResult) {
    $deliveredDate = '';
    for($i = 0; $i < count($orders); $i++) {
      if(date('Y-m-d', strtotime($deliveredDate)) !== date('Y-m-d', strtotime($orders[$i]['deliveredDate']))) {
        $deliveredDate = $orders[$i]['deliveredDate'];
        $dispatchResult['data'][] = $this->isdeliveredDateInArray($orders, $orders[$i]['deliveredDate']);
      }      
    }
  }

  /**
   * Dispactch des élément des commandes
   *
   * @param array $orders
   * @param string $deliveredDate
   * @return void
   */
  function isdeliveredDateInArray(array $orders, string $deliveredDate) {

    $sameDeliveredDateOrders = [];
    $docType ='docType';

    foreach($orders as $order) {
      if(date('Y-m-d', strtotime($order['deliveredDate'])) === date('Y-m-d', strtotime($deliveredDate))) {
        $sameDeliveredDateOrders[]= $order;
      }
    }

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
    $this->createXls($deliveredDate, $xlsOrderArray, $docType, date('Y-m-d'));
    
    return [
      'deliveredDate' => $deliveredDate,
      'orders' => $xlsOrderArray
    ];
  }

  /**
   * Génération d'un fichier XLS
   *
   * @param [type] $deliveredDate
   * @param [type] $xlsOrderArray
   * @param [type] $docType
   * @param [type] $orderDate
   * @return void
   */
  function createXls($deliveredDate, $xlsOrderArray, $docType, $orderDate) {
     // Test création XLS
     if(count($xlsOrderArray) > 0) {
      CreateXlsOrder($docType, $orderDate, $deliveredDate, $xlsOrderArray);
    }
  }

}