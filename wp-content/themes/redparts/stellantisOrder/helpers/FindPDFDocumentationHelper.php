<?php
require_once ('/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/services/MySqlDocumentationOrderRepository.php');
require_once ('/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/services/MySqlOrderPdfRepository.php');
require_once ('/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/services/MySqlPartNumberToPdfRepository.php');
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/interfaces/DocumentationOrderInterface.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/interfaces/OrderPDFInterface.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/interfaces/PartNumberToPDFInterface.php';

class FindPDFDocumentationHelper {

  /**
   * PartNumber
   *
   * @var string
   */
  protected string $partNumber;

  protected array $partNumberToPdfs = [];

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

  function __construct(
    DocumentationOrderInterface $documentationOrderRepository,
    OrderPDFInterface $orderPdfRepository,
    PartNumberToPDFInterface $partNumberToPDFRepository,
    string $partNumber
  ) {
    $this->documentationOrderRepository = $documentationOrderRepository;
    $this->orderPdfRepository = $orderPdfRepository;
    $this->partNumberToPDFRepository = $partNumberToPDFRepository;
    $this->partNumber = $partNumber;    
  }

  /**
   * Recherche des linkPDF associé au partNumber 
   *
   * @return void
   */
  function findAllPartNumberDocumentation() {
    $this->partNumberToPDFRepository->findByPartNumber($this->partNumber);
  }

  /**
   * Recherche des liens PDF
   *
   * @return void
   */
  function findAllPDFLink() {

  }


}