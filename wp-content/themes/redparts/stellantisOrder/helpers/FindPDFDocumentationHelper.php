<?php
require_once ('/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/services/MySqlDocumentationOrderRepository.php');
require_once ('/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/services/MySqlOrderPdfRepository.php');
require_once ('/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/services/MySqlPartNumberToPdfRepository.php');
require_once ('/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/services/MySqlPdfPrintRepository.php');
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/interfaces/PdfPrintRepositoryInterface.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/interfaces/DocumentationOrderInterface.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/interfaces/OrderPDFInterface.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/interfaces/PartNumberToPDFInterface.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/model/pdfModel/OrderPdfModel.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/model/pdfModel/DocumentationOrderModel.php';

/**
 * Helper Récupérations Liens PDF par PartNumber
 */
class FindPDFDocumentationHelper {

  // Documentation intéreur ou extérieur
  const PDF_INT_COUV_ARRAY = [
    'int',
    'couv'
  ];

  // Documentation REF
  const DOCUMENTATION_REFERENCE_ARRAY = [
    'SBLangFile',
    'QGLangFile'
  ];

  /**
   * PartNumber
   *
   * @var string
   */
  protected string $partNumber;

  /**
   * liste des liaison partNumber - Documentation
   *
   * @var array
   */
  protected array $documentationOrders = [];
  
   /**
   * OrderPDFInterface Repository
   *
   * @var DocumentationOrderInterface
   */
  protected DocumentationOrderInterface $documentationOrderRepository;

  /**
   * OrderPDFInterface Repository
   *
   * @var OrderPDFInterface
   */
  protected OrderPDFInterface $orderPdfRepository;  

  /**
   * PartNumberToPDFInterface Repository
   *
   * @var PartNumberToPDFInterface
   */
  protected PartNumberToPDFInterface $partNumberToPDFRepository;

  /**
   * Repository PdfPrintRepositoryInterface
   *
   * @var PdfPrintRepositoryInterface
   */
  protected PdfPrintRepositoryInterface $PdfPrintRepository;

  function __construct(
    RepositoriesModel $repositories,
    string $partNumber
  ) {
    $this->documentationOrderRepository = $repositories->getDocumentationOrder();    
    $this->orderPdfRepository = $repositories->getOrderPdfRepository();
    $this->partNumberToPDFRepository = $repositories->getPartNumberToPDFRepository();
    $this->PdfPrintRepository = $repositories->getPdfPrintRepository();
    $this->partNumber = $partNumber;    
  }

  /**
   * Renvoie la liste DocumentationOrders
   *
   * @return array
   */
  function getDocumentationOrders(): array {
    return $this->documentationOrders;
  }

  /**
   * Recherche des linkPDF associé au partNumber 
   *
   * @return void
   */
  function findPartNumberToPdf() {
    $partNumberToPdfs = $this->partNumberToPDFRepository->findByPartNumber($this->partNumber);

    // TODO Si pas de données pour la documentation PDF
    if(count($partNumberToPdfs) === 0) {

    }

    foreach($partNumberToPdfs as $partNumberToPdf) {
      
      // Verification que les données QG et SB ne sont vide 
      if(empty($partNumberToPdf['SBLangFile']) || empty($partNumberToPdf['QGLangFile'])) {
        return new \Exception('QGLangFile or SBLangFile incomplete', 500);
      }

      // Boucle sur DOCUMENTATION_REFERENCE (SBLangFile ou QGLangFile)
      foreach(self::DOCUMENTATION_REFERENCE_ARRAY as $DOCUMENTATION_REFERENCE) {

        // Liste des liens PDF  INT et COUV pour une DOCUMENTATION_REFERENCE (SBLangFile ou QGLangFile) 
        $pdfPrints =  $this->findPdfPrint($partNumberToPdf, $DOCUMENTATION_REFERENCE);

        // Crétion d'un nouveau DocumentationOrderModel et ajout dans la liste
        $this->addToDocumentationOrderList($partNumberToPdf, $DOCUMENTATION_REFERENCE, $pdfPrints);
      }
    }
  } 

  /**
   * Recherche des liens PDF
   * @param array $partNumberToPdf
   * @param string $DOCUMENTATION_REFERENCE - SBLangFile ou QGLangFile
   * @return array
   */
  private function findPdfPrint(array $partNumberToPdf, $DOCUMENTATION_REFERENCE): array {

    // Stock les liens PDF 
    $orderPdfs = [];

    // Recherche PDF intérieur et extérieur
    foreach(self::PDF_INT_COUV_ARRAY as $PDF_INT_COUV) {
      $orderPdfs [] = $this->AddToOrderPdfList($partNumberToPdf, $DOCUMENTATION_REFERENCE, $PDF_INT_COUV);  
    }

    return $orderPdfs;
  }
  
  /**
   * Renvoie d'un OrderPdfModel
   * @param array $partNumberToPdf
   * @param string $DOCUMENTATION_REFERENCE - SBLangFile ou QGLangFile
   * @param string $PDF_INT_COUV - INT ou COUV
   *
   * @return OrderPdfModel
   */
  private function AddToOrderPdfList(array $partNumberToPdf, string $DOCUMENTATION_REFERENCE, string $PDF_INT_COUV): OrderPdfModel {
    // Recherche link pdf
    $linkPDF = $this->PdfPrintRepository->findByLinkName($partNumberToPdf[$DOCUMENTATION_REFERENCE], $PDF_INT_COUV);

    if(empty($linkPDF)) {
      return new OrderPdfModel(-1, false);
    }

    // Ajout dans la liste des liens PDF
    return new OrderPdfModel($linkPDF['id'], true);   
  }

  /**
   * Création d'un nouveau 
   *
   * @param array $partNumberToPdf
   * @param string $DOCUMENTATION_REFERENCE
   * @param array $pdfPrints
   * @return void
   */
  private function addToDocumentationOrderList(array $partNumberToPdf, string $DOCUMENTATION_REFERENCE, array $pdfPrints) {
    // Ajout d'un nouveau documentationOrderModel dans  la liste
    $this->documentationOrders [] = new DocumentationOrderModel($partNumberToPdf['id'], $partNumberToPdf[$DOCUMENTATION_REFERENCE], $pdfPrints);
  }
}