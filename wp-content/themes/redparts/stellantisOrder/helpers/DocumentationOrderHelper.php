<?php
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/model/DocumentationOrderModel.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/model/RepositoriesModel.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/interfaces/DocumentationOrderRepositoryInterface.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/interfaces/DocPDFRepositoryInterface.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/utils/StaticData.php';
require_once ('/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/model/Order.php');

/**
 * Recherche des documentation PDF associées aux Commandes
 */
class DocumentationOrderHelper {

  /**
   * Liste des documentations PDF pour une commnande
   *
   * @var array
   */
  protected array $documentationOrders = [];

  /**
   * DocPDF Repository
   *
   * @var DocPDFRepositoryInterface
   */
  protected DocPDFRepositoryInterface $docPDFRepository;

  /**
   * DocumentationOrder Repository
   *
   * @var DocumentationOrderRepositoryInterface
   */
  protected DocumentationOrderRepositoryInterface $documentationOrderRepository;

  /**
   * Commande
   *
   * @var Order
   */
  protected Order $order;

  function __construct(RepositoriesModel $repositories, Order $order) {
    $this->docPDFRepository = $repositories->getDocPDFRepository();
    $this->documentationOrderRepository = $repositories->getDocumentOrderRepository();
    $this->order = $order;

    // Réinitialise la liste documentationOrders
    $this->documentationOrders = [];
  }

  /**
   * Recherche toutes les documentations PDF liées a une commande
   *
   * @param Order $order
   * @return void
   */
  function findAllOrderPDFDocumentations(): void {
    foreach(StaticData::PDFS as $doc) {
      $documentation = $this->findPDFDocumentation($doc);

      // Ajout de la docuementation dans la liste des document de la commande
      if($documentation) {
        $this->documentationOrders[] = $documentation;
      }
    }    
  }

  /**
   * Vérification si PDF de manquant pour la commande
   *
   * @return bool
   */
  function arePDFDocumentationMissing(): bool {
    foreach($this->documentationOrders as $documentation) {

      // Vérification Insance
      if(!$documentation instanceof DocumentationOrderModel) {
        throw new \Exception('Error on PDF Documentation');
      }

      if(!$documentation->getIsDocumentationFind()) {
        return false;
      }
    }
    return true;
  }

  /**
   * Recherche d'une documentation PDF en base de données
   *
   * @param Array $documentationParam - Données sur la documentation a trouver
   * @return DocumentationOrderModel|null
   */
  private function findPDFDocumentation(Array $documentationParam ) {
   
    $documentation = $this->docPDFRepository->findMaintenanceBook($this->order);

    // Documentation manquante
    if(!$documentation) {
      return new DocumentationOrderModel(
        $documentationParam['DOC_TYPE'], 
        $documentationParam['DOC_SUB_TYPE'], 
        '', 
        '', 
        false
      );
    } 

    // Docuementation trouvée
    return new DocumentationOrderModel(
      $documentationParam['DOC_TYPE'], 
      $documentationParam['DOC_SUB_TYPE'], 
      $documentation['link'], 
      $documentation['id'], 
      true
    );
  }

  private function saveDocumentOrder(int $orderId, DocumentationOrderModel $documentationOrder) {

  }
}