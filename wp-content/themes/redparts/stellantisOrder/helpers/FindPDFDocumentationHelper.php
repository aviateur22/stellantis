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

class FindPDFDocumentationHelper {

  // Docuementation intéreur ou extérieur
  const PDF_INT_COUV = [
    'INT' => 'INT',
    'COUV' => 'COUV'
  ];

  // Documentation REF
  const DOCUMENTATION_REFERENCE = [
    'SB_LANG_FILE' => 'SBLangFile',
    'QG_LANG_FILE' => 'QGLangFile'
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
   * Liste des liasons documentations - lien PDF 
   *
   * @var array
   */
  protected array $orderPdfs = [];

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
   * Recherche des linkPDF associé au partNumber 
   *
   * @return void
   */
  function findPartNumberToPdf() {
    $partNumberToPdfs = $this->partNumberToPDFRepository->findByPartNumber($this->partNumber);

    // Si pas de données pour la documentation PDF
    if(count($partNumberToPdfs) === 0) {

    }

    foreach($partNumberToPdfs as $partNumberToPdf) {
      
      // Verification que les données QG et SB ne sont vide 
      if(empty($partNumberToPdf['SBLangFile']) || empty($partNumberToPdf['QGLangFile'])) {
        return new \Exception('QGLangFile ou SBLangFile incomplete', 500);
      }

      // Parcours de REF de la docuementation
      foreach(self::DOCUMENTATION_REFERENCE as $REF) {
        $this->findPdfPrint($partNumberToPdf, $REF);  
        //$this->addToDocumentationOrderList($partNumberToPdf);
      }
    }
  } 

  /**
   * Recherche des liens PDF
   * @param array $partNumberToPdf
   * @return void
   */
  function findPdfPrint(array $partNumberToPdf, $REF) {
    // Recherche PDF intérieur et extérieur
    foreach(self::PDF_INT_COUV as $PDF_INT_COUV) {
      $this->AddToOrderPdfList($partNumberToPdf, $REF, $PDF_INT_COUV);  
    }
  }
  
  /**
   * Ajout dans la list des PDF link
   * @param array $partNumberToPdf
   * @param string $REF - SBLangFile ou QGLangFile
   * @param string $PDF_INT_COUV - INT ou COUV
   *
   * @return void
   */
  function AddToOrderPdfList(array $partNumberToPdf, string $REF, string $PDF_INT_COUV) {

    $isPdfFind = true;

    // Recherche link pdf
    $linkPDF = $this->PdfPrintRepository->findByLinkName($partNumberToPdf[$REF], $PDF_INT_COUV);

    if(empty($linkPDF)) {
      $isPdfFind = false;
    }

    // Ajout dans la liste des liens PDF
    $this->orderPdfs [] = new OrderPdfModel($linkPDF['id'], $isPdfFind);
  }


}