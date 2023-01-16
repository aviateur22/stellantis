<?php
use FTP\Connection;
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/interfaces/OrderTransferInterface.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/interfaces/OrderRepositoryInterface.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/services/MySqlOrderRepository.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/model/FormatedOrder.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/exceptions/FtpException.php';

/**
 * Transfert des fichier via FTP
 */
class FtpTransfert implements OrderTransferInterface {

  const RECEPIENT_INFORMATION = [
    'HOST' => '5.196.28.34',
    'USER' => 'maury',
    'PASSWORD' => 'tWa12?op78!',
    'DESTINATION_FILE_PATH' => 'TEST/',
  ];

  /**
   * Repository Order
   *
   * @var OrderRepositoryInterface
   */
  protected OrderRepositoryInterface $orderRepository;

  /**
   * OrderId
   *
   * @var string
   */
  protected string $orderId;

  function __construct(OrderRepositoryInterface $orderRepository, string $orderId) {
    $this->orderRepository = $orderRepository;
    $this->orderId = $orderId;
  }

  /**
   * Transfert des commandes formatée vers le client final
   *
   * @param array $formatedOrders - Array de Object FormatedOrder
   * @return void
   */
  function transfertOrders(array $formatedOrders): void {

    foreach($formatedOrders as $formatedOrder) {
      if($formatedOrder instanceof FormatedOrder) {

        // Récupération données pour envoie
        $ftpConnect = $this->getFactoryRecipient($formatedOrder->getOrderQuantity());

        // Transfert du fichier
        ftp_put(
          $ftpConnect, 
          self::RECEPIENT_INFORMATION['DESTINATION_FILE_PATH'].$formatedOrder->getFileName(), 
          $formatedOrder->getOrderFilePath(), 
          FTP_ASCII
        );
        
        // close
        ftp_close($ftpConnect);
      }
    }
  }

  /**
   * Récupération addresse d'envoie
   *
   * @param int $OrderQuantity - quantité a imprimer
   * @return Connection|false
   */
  function getFactoryRecipient(int $orderQuantity): Connection
  {    
    switch ($orderQuantity) {
      case $orderQuantity < 100:
        // Connection FTP
        $ftpConnect = ftp_connect(
          self::RECEPIENT_INFORMATION['HOST'], 21) or die("Error connecting to ftp $ftpConnect");

        // Login FTP
        ftp_login(
          $ftpConnect, 
          self::RECEPIENT_INFORMATION['USER'], 
          self::RECEPIENT_INFORMATION['PASSWORD']
        );
        return $ftpConnect;
        break;

      case $orderQuantity > 100:
       // Connection FTP
       $ftpConnect = ftp_connect(
        self::RECEPIENT_INFORMATION['HOST'], 21) or die("Error connecting to ftp $ftpConnect");

        // Login FTP
        ftp_login(
          $ftpConnect,
          self::RECEPIENT_INFORMATION['USER'],
          self::RECEPIENT_INFORMATION['PASSWORD']
        );
        return $ftpConnect;
        break;

      default: 
      throw new FtpException();
      break;
    }
  }

  /**
   * Modifie le status des Commandes
   *
   * @return void
   */
  function updateOrderStatus(): void  {
    // Mise a jour du statut des commandes
    $this->orderRepository->updateWip(ORDER_STATUS['PREFLIGHT'], $this->orderId);
  }
}