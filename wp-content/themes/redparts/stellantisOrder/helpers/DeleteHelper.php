<?php
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/interfaces/OrderRepositoryInterface.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/services/MySqlOrderRepository.php';
require_once ('/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/model/RepositoriesModel.php');

/**
 * Suppression des ancienne données
 */
class DeleteHelper {

  /**
   * Path pour sauvegarder les commandes générées
   */
  const SAVE_XML_PATH = '/home/mdwfrkglvc/www/wp-content/uploads/xml/';

  /**
   * Undocumented variable
   *
   * @var OrderRepositoryInterface
   */
  protected OrderRepositoryInterface $orderRepository;


  function __construct(RepositoriesModel $repositories)
  {
    $this->orderRepository = $repositories->getOrderRepository();  
  }

  /**
   * Suppression des ancienne commandes non traitées
   *
   * @return void
   */
  function deleteUnusedOrders() {
    $this->orderRepository->deleteUnused(date('Y-m-d 00:00:00'));
  }

  /**
   * Supprimes les anciennes commandes générées pour les usines
   *
   * @return void
   */
  function deleteOldgeneratedOrderFile() {

    // Récupération des noms de fichier
    $files = scandir(self::SAVE_XML_PATH);

    // Boucle sur les fichiers
    foreach($files as $file) {
      // Path pour acceder au fichier
      $filePath = self::SAVE_XML_PATH.$file;

      // Récupération date de création
      $fileCreatedAt = filemtime($filePath);

      // Si date création du fichier < a date du jour

      if(date('Y-m-d', $fileCreatedAt) < date('Y-m-d') && $file !== "..") {
        unlink($filePath);
      }      
    }
  }
}