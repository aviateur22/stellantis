<?php
use FTP\Connection;
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/interfaces/OrderTransferInterface.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/interfaces/OrderRepositoryInterface.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/services/MySqlOrderRepository.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/model/FormatedOrder.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/exceptions/FtpException.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/utils/StaticData.php';

/**
 * Transfert des fichier via FTP
 */
class FtpTransfert implements OrderTransferInterface {

  const RECEPIENT_INFORMATION = [
    'HOST' => '5.196.28.34',
    'USER' => 'maury',
    'PASSWORD' => 'tWa12?op78!',
    'DESTINATION_FILE_PATH' => 'STELLANTIS/40_orderSubmission/'
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
  function transfertOrders(array $formatedOrders): bool {

    foreach($formatedOrders as $formatedOrder) {
      // Format des données invalide
      if(!$formatedOrder instanceof FormatedOrder) {
        throw new InvalidFormatException();
      }
      
      // Récupération données pour envoie
      $this->getFactoryRecipient($formatedOrder);            
    }

    return true;
  }

  /**
   * Récupération addresse d'envoie
   *
   * @param FormatedOrder $formatedOrder - Parametre de transfere de la commande
   * @return void
   */
  function getFactoryRecipient(FormatedOrder $formatedOrder): void {    
    $ftpConnect = null; 

    switch ($formatedOrder->getOrderQuantity()) {
      case $formatedOrder->getOrderQuantity() < 100:
        // Connection FTP
        $ftpConnect = ftp_connect(
          self::RECEPIENT_INFORMATION['HOST'], 21) or die("Error connecting to ftp $ftpConnect");

        // Login FTP
        ftp_login(
          $ftpConnect, 
          self::RECEPIENT_INFORMATION['USER'], 
          self::RECEPIENT_INFORMATION['PASSWORD']
        );

      break;

      case $formatedOrder->getOrderQuantity() >= 100:
        // Connection FTP
        $ftpConnect = ftp_connect(
          self::RECEPIENT_INFORMATION['HOST'], 21) or die("Error connecting to ftp $ftpConnect");

        // Login FTP
        ftp_login(
          $ftpConnect,
          self::RECEPIENT_INFORMATION['USER'],
          self::RECEPIENT_INFORMATION['PASSWORD']
        );
        break;

      default:        
          throw new FtpException();
      break;
    }

    if(!$ftpConnect) {
      throw new FtpException();
    }

    // Force FTP passive mode
    ftp_pasv($ftpConnect, true) or die("Unable switch to passive mode");

    // Vérification existance fichier source
    if(!file_exists($formatedOrder->getOrderFilePath())) {
      throw new \FileNotFindException('Le fichier source n\'existe pas');
    }
    
   // Récupéaration File Extension
    $fileExtension = $this->getFileInformation($formatedOrder->getOrderFilePath());
    
    // Transfert du fichier
    ftp_put(
      $ftpConnect,
      self::RECEPIENT_INFORMATION['DESTINATION_FILE_PATH'].$formatedOrder->getFileName().'.'. $fileExtension, 
      $formatedOrder->getOrderFilePath(),
      FTP_ASCII
    );
    
    // close
    ftp_close($ftpConnect);
  }
  
  /**
   * Récuperation exension du fichier a transférer
   *
   * @param string $filePath
   * @return string
   */
  private function getFileInformation(string $filePath): string {    
    $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);
    return $fileExtension;
  }

  /**
   * Modifie le status des Commandes
   *
   * @return void
   */
  function updateOrderStatus(): void  {
    
    // Mise a jour du statut des commandes
    $this->orderRepository->updateWip($this->orderId);
  }
}