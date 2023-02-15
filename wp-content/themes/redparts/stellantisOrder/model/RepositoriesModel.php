<?php
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/interfaces/OrderRepositoryInterface.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/interfaces/ModelRepositoryInterface.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/interfaces/ForecastRepositoryInterface.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/interfaces/DocumentationOrderInterface.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/interfaces/OrderPDFInterface.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/interfaces/PartNumberToPDFInterface.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/interfaces/PdfPrintRepositoryInterface.php';


/**
 * Modèle sur les base de données
 */
class RepositoriesModel  {

  /**
   * Order Repository
   *
   * @var OrderRepositoryInterface
   */
  protected OrderRepositoryInterface $orderRepository;
  
  /**
   * Model Repository
   *
   * @var ModelRepositoryInterface
   */
  protected ModelRepositoryInterface $modelRepository;

  /**
   * Forecast Repository
   *
   * @var ForecastRepositoryInterface
   */
  protected ForecastRepositoryInterface $forecastRepository;

  /**
   * DocumentationOrder Repository
   *
   * @var DocumentationOrderInterface
   */
  protected DocumentationOrderInterface $documentationOrderRepository;

  /**
   * OrderPDF Repository
   *
   * @var OrderPDFInterface
   */
  protected OrderPDFInterface $orderPdfRepository;  

  /**
   * PartNumberToPDF Repository
   *
   * @var PartNumberToPDFInterface
   */
  protected PartNumberToPDFInterface $partNumberToPDFRepository;

  /**
   * PartNumberToPDF Repository
   *
   * @var PdfPrintRepositoryInterface
   */
  protected PdfPrintRepositoryInterface $pdfPrintRepository;


  function __construct(
    OrderRepositoryInterface $orderRepository,    
    ModelRepositoryInterface $modelRepository,
    ForecastRepositoryInterface $forecastRepository,
    OrderPDFInterface $orderPdfRepository,
    DocumentationOrderInterface $documentationOrderRepository,
    PartNumberToPDFInterface $partNumberToPDFRepository,
    PdfPrintRepositoryInterface $pdfPrintRepository
  ) {
    $this->orderRepository = $orderRepository;
    $this->modelRepository = $modelRepository;   
    $this->forecastRepository = $forecastRepository;
    $this->orderPdfRepository = $orderPdfRepository;
    $this->documentationOrderRepository = $documentationOrderRepository;
    $this->partNumberToPDFRepository = $partNumberToPDFRepository;
    $this->pdfPrintRepository = $pdfPrintRepository;
  }

  #region getters

  /**
   * Get Repository Order
   *
   * @return OrderRepositoryInterface
   */
  function getOrderRepository(): OrderRepositoryInterface {
    return $this->orderRepository;
  }

  /**
   * Get Repository ForecastRepository
   *
   * @return ForecastRepositoryInterface
   */
  function getForecastRepository(): ForecastRepositoryInterface {
    return $this->forecastRepository;
  }

  /**
   * Get Model Repository
   *
   * @return ModelRepositoryInterface
   */
  function getModelRepository(): ModelRepositoryInterface {
    return $this->modelRepository;
  }

  function getPartNumberToPDFRepository(): PartNumberToPDFInterface {
    return $this->partNumberToPDFRepository;
  }

  function getOrderPdfRepository(): OrderPDFInterface {
    return $this->orderPdfRepository;
  }

  function getDocumentationOrderRepository(): DocumentationOrderInterface {
    return $this->documentationOrderRepository;
  }

  function getPdfPrintRepository(): PdfPrintRepositoryInterface {
    return $this->pdfPrintRepository;
  } 
  #endRegion
}