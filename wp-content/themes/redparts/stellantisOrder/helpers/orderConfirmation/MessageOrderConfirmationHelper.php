<?php
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/utils/OrderPdf.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/interfaces/OrderRepositoryInterface.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/interfaces/MailServiceInterface.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/model/RepositoriesModel.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/services/MySqlOrderRepository.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/helpers/orderConfirmation/OrderFormConfirmationHelper.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/html/EmailTemplateOrderConfirmation.php';

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
     * Liste des fichiers Xlsx généré
     *
     * @var array
     */
    protected array $tabPieceJointe;

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

    // Génération des bons de commandes au Format XLS et récupération des liens
    $this->tabPieceJointe = $this->orderFormConfirmationHelper->dispacthOrderBetweenMiAndMA($this->orders);
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
        'message'=> $messageForManchecourt,
        'email' => 'cthuaudet@keygraphic.fr'
      ],
      [
        'to' => StaticData::MILLAU_FACTORY_NAME,
        'message' => $messageForMillau,
        'email' => 'cthuaudet@keygraphic.fr'
      ]
    ];
  }

  /**
   * Envoie des emails de commandes
   *
   * @return void
   */
  function sendMessage() {
    $headers [] = 'From: Plateforme STELLANTIS <admin@mdw-05.fr>';
   $headers [] = 'Content-Type: text/html; charset=utf-8';
    foreach($this->orderMessages as $orderMessage) {
      if(!empty($orderMessage['message'])) {

        // Composition du message avec introduction + conclusion
      //  $message = $this->initMessage(). $orderMessage['message'] .$this->endMessage();
        $message = EmailTemplateOrderConfirmation::getTemplate($orderMessage['message']);

        // Envoi email
        //wp_mail($orderMessage['email'], 'new order', $message);
        wp_mail($orderMessage['email'], 'Stellantis new order', $message,$headers,$this->tabPieceJointe);
      //  $this->mailService->sendMessage($orderMessage['email'], 'new order', $message);
      }
    }
  }

  /**
   * Message résumé d'une commande
   *
   * @param array $order
   * @return string
   */
  function formatMessage(array $order, int $index): string {

    $orderMessage  = '<p style="line-height:110%">'.'Record N°: ' . $index .'</p>';
    $orderMessage .= '<p style="line-height:110%">'.'PartNumber: ' . $order['partNumber'] .'</p>';
    $orderMessage .= '<p style="line-height:110%">'.'Brand: ' . $order['brand'] .'/'. $order['model'] .'/'. $order['year'] .'/'. $order['verion'] .'</p>';
    $orderMessage .= '<p style="line-height:110%">'.'Quantity: ' .$order['quantity'].'</p>';
    $orderMessage .= '<p style="line-height:110%">'.'Forecast quantity: ' .$order['forecastPrint'].'</p>';
    $orderMessage .= '<p style="line-height:110%">'.'Delivered date: ' .$order['deliveredDate'].'</p>';
    $orderMessage .= '<p style="line-height:110%">'.$this->formatPdfLinkMessage($order) .'</p>';

    $orderMessage .= '<br>';

    return $orderMessage;

  }

  /**
   * Début du message
   *
   * @return void
   */
  function initMessage() {
    return 'Bonjour,

    Une nouvelle commande vient d’être déposée sur la plateforme STELLANTIS'.'</p>';
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
      $pdfTextMessage .= '<p style="line-height:110%">'.$pdfLink . ':  Quantity ='.$order['quantity']. '</p>';
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
