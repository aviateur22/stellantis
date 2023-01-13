<?php
use FTP\Connection;
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/interfaces/OrderTransferInterface.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/interfaces/OrderRepositoryInterface.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/services/MySqlOrderRepository.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/model/FormatedOrder.php';

/**
 * Transfert des fichier via FTP
 */
class FtpTransfert implements OrderTransferInterface {

  const RECEPIENT_INFORMATION = [
    'HOST' => 'localhost',
    'USER' => 'aviateur22',
    'PASSWORD' => 'Advency1',
    'DESTINATION_FILE_PATH' => 'ddd',
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
        ftp_put($ftpConnect, self::RECEPIENT_INFORMATION['DESTINATION_FILE_PATH'].'/'.$formatedOrder->getFileName(), $formatedOrder->getOrderFilePath(), FTP_ASCII);
        
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

        $ftpConnect = ftp_connect(self::RECEPIENT_INFORMATION['HOST']) or die("Error connecting to ftp $ftpConnect");
        ftp_login($ftpConnect, self::RECEPIENT_INFORMATION['USER'], self::RECEPIENT_INFORMATION['PASSWORD']);
        return $ftpConnect;
        break;

      case $orderQuantity > 100:
        $ftpConnect = ftp_connect(self::RECEPIENT_INFORMATION['HOST']) or die("Error connecting to ftp $ftpConnect");
        ftp_login($ftpConnect, self::RECEPIENT_INFORMATION['USER'], self::RECEPIENT_INFORMATION['PASSWORD']);
        return $ftpConnect;
        break;

      default: 
      $ftpConnect = ftp_connect(self::RECEPIENT_INFORMATION['HOST']) or die("Error connecting to ftp $ftpConnect");
      ftp_login($ftpConnect, self::RECEPIENT_INFORMATION['USER'], self::RECEPIENT_INFORMATION['PASSWORD']);
      return $ftpConnect;
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