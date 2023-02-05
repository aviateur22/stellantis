<?php
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/utils/OrderPdf.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/interfaces/OrderRepositoryInterface.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/interfaces/MailServiceInterface.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/model/RepositoriesModel.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/services/MySqlOrderRepository.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/helpers/orderConfirmation/OrderFormConfirmationHelper.php';

/**
 * Helper Formate message pour envoie Email a MI ou MA
 */
class MessageOrderConfirmationHelper {

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
   * Liste des messages à envoyer
   *
   * @var array
   */
  protected array $orderMessages;

  /**
   * Génération bon de commande Fichier XLS
   *
   * @var OrderFormConfirmationHelper
   */
  protected OrderFormConfirmationHelper $orderFormConfirmationHelper;

  function __construct(
    string $orderId, 
    RepositoriesModel $repositories, 
    MailServiceInterface $mailService,
    OrderFormConfirmationHelper $orderFormConfirmationHelper
    ) {
    $this->orderRepository = $repositories->getOrderRepository();
    $this->orderFormConfirmationHelper = $orderFormConfirmationHelper;
    $this->mailService = $mailService;
    $this->orderId = $orderId;
  
    // Récupération des commandes
    $this->initialize();
  }

  function initialize() {
    // Récupération des commandes
    $this->getOrders();

    // Génération des bons de commandes au Format XLS
    $this->orderFormConfirmationHelper->dispacthOrderBetweenMiAndMA($this->orders);
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

      // Si message pour Millau
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
    return $orderMessage;
    
  }

  /**
   * Début du message
   *
   * @return void
   */
  function initMessage() {
    return 'Bonjour, 

    Une nouvelle commande vient d\’être déposée sur la plateforme STELLANTIS'.'\r\n'.'\r\n';
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
  protected function getOrders(): void {
    $this->orders = $this->orderRepository->findAllByOrderId($this->orderId);
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