<?php
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/helpers/OrderHelper.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/helpers/ExcelFileHelper.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/interfaces/OrderSourceInterface.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/interfaces/DocumentationOrderInterface.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/interfaces/OrderPDFInterface.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/model/RepositoriesModel.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/model/pdfModel/DocumentationOrderModel.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/model/pdfModel/OrderPdfModel.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/helpers/FindPDFDocumentationHelper.php';

class OrderFromExcelFile extends ExcelFileHelper implements OrderSourceInterface {

  /**
   * Structure du docuement Excel pour les commandes
   *
   */
  const FILE_STRUCTURE = [
    'ROW_START' => 7,
    'COLUMN_START' => 1
  ];

  /**
   * Données permettant de valider le document Excel
   * Espaces entre les mots supprimés
   * Text en lowercase
   *
   */
  const VALIDATE_WORDS = [
    [
      'WORD' => 'brand',
      'ROW' => 1,
      'COLULMN' => 0
    ],
    [
      'WORD' => 'carlinename',
      'ROW' => 1,
      'COLULMN' => 2
    ],
    [
      'WORD' => 'codepochett',
      'ROW' => 6,
      'COLULMN' => 0
    ],
    [
      'WORD' => 'package',
      'ROW' => 6,
      'COLULMN' => 1
    ],
    [
      'WORD' => 'carline',
      'ROW' => 6,
      'COLULMN' => 2
    ],    
    [
      'WORD' => 'modelyear',
      'ROW' => 1,
      'COLULMN' => 4
    ]
  ];

  /**
   * Liste des données communes au fichier de commandes
   *
   * @var array
   */
  private array $inCommonInformations = [];

  /**
   * Order $orders
   *
   * @var array
   */
  protected array $orders = []; 

  /**
   * Helper pour vérification des commandes
   *
   * @var OrderHelper
   */
  protected OrderHelper $orderHelper;

  /**
   * Utilisateur
   *
   * @var User
   */
  protected User $user;

  /**
   * Order repository
   *
   * @var OrderRepositoryInterface
   */
  protected OrderRepositoryInterface $orderRepository;

  /**
   * DocumentationOrder repository
   *
   * @var DocumentationOrderInterface
   */
  protected DocumentationOrderInterface $documentationOrderRepository;

  /**
   * OrderPDFs Repository
   *
   * @var OrderPDFInterface
   */
  protected OrderPDFInterface $orderPDFRepository;

  function  __construct(string $fileName, OrderHelper $orderHelper, User $user, RepositoriesModel $repositories) {
    // Constructeur parent
    parent::__construct($fileName); 

    $this->orderHelper = $orderHelper;
    $this->user = $user;
    $this->orderRepository = $repositories->getOrderRepository();
    $this->documentationOrderRepository = $repositories->getDocumentationOrderRepository();
    $this->orderPDFRepository = $repositories->getOrderPdfRepository();
  } 

  /**
   * Nouveau StdClass pour stocker les données d'une commande
   *
   * @return void
   */
  function initializeFile() {
    // Vérification disponibilité PHPExcel
    $this->isPhpExcelAvailbale();

    // Récupération page Active des commandes
    $this->getActiveSheet();

    // Vérification de validité du fichier
    $this->isSheetValid(self::VALIDATE_WORDS, 'Provided XLS file not valid for Orders');

    // Récupération des données communes a toutes les commandes
    $this->getInCommonInformations();
    
    // Lecture du fichier
    $this->readOrderSourceData();

    // Sauvegarde des commandes
    //$this->orderRepository->saveList($this->orders);

    // TODO New Save
    $this->saveOrders();
  }

  /**
   * Parcours le contenu du fichier
   *
   * @return void
   */
  function readOrderSourceData(): void {
   
    // Derniere ligne
    $LAST_ROW = $this->activeSheet->getHighestRow();

    for($row = self::FILE_STRUCTURE['ROW_START']; $row < $LAST_ROW; $row++) {     
      
      if($this->readCellValue($row, 0) !== '') {
        // Nouveau OrderStdClass
        $orderStdClass = $this->getNewOrderStdClass();

        // CoverCode
        $orderStdClass->coverCode = $this->readCellValue($row, 0);

        // PartNumber
        $orderStdClass->partNumber = $this->readCellValue($row, 1);

        //Model Name
        $orderStdClass->modelName = $this->readCellValue($row, 2);

        // Quantity
        $orderStdClass->quantity = $this->readCellValue($row, 3);

        // DeliveredDate
        $orderStdClass->deliveredDate = $this->readCellValue($row, 4, true);
    
        // Validation des proprité des commandes
        $this->orderHelper->areOrderPropertiesValid($orderStdClass);

        // Ajout de la commande
        $this->orders[] = $this->orderHelper->createOrder();
        
      }    
    }    
  }

  /**
   * Sauvegarde des commandes
   *
   * @return void
   */
  function saveOrders() {
    foreach($this->orders as $order) {
      // Vérification instance
      if(!$order instanceof Order) {
        throw new \Exception('order is not instanceOf Order');
      }

      // Ajout de la commande
      $orderId = $this->orderRepository->save($order);       
      
      // Ajout Liaison Commande - PartNumberToPDF
      foreach($order->getPdfDocumentations() as $documentationOrder) {

        if(!$documentationOrder instanceof DocumentationOrderModel) {
          throw new \Exception('documentationOrder is not instanceOf DocumentationOrderModel');
        }

        $this->saveDocumentationOrders($documentationOrder, $orderId);
      }      
    }
  }

  /**
   * Sauvegarde des DocuementationOrder
   *
   * @return void
   */
  function saveDocumentationOrders(DocumentationOrderModel $documentationOrder,int $orderId) {
    // Ajout Liaison Commande - PartNumberToPDF
    $documentationOrderId = $this->documentationOrderRepository->save($documentationOrder, $orderId);
   
    // Ajout Liaison Docuementation - PDF LINK
    foreach($documentationOrder->getOrderPdfs() as $orderPdf) {

      if(!$orderPdf instanceof OrderPdfModel) {
        throw new \Exception('orderPdf is not instanceOf OrderPdfModel');
      }

      $this->saveOrderPdf($orderPdf, $documentationOrderId, $orderId);
    }
  }

  /**
   * Sauvegarde d'une liaison PDF - DocumentationOrder - OrderId 
   *
   * @param OrderPdfModel $orderPdf
   * @param integer $documentationOrderId
   * @param integer $orderId
   * @return void
   */
  function saveOrderPdf(OrderPdfModel $orderPdf, int $documentationOrderId, int $orderId) {
    // Sauvegarde 
    $this->orderPDFRepository->save($orderPdf, $orderId , $documentationOrderId);
  }

  /**
   * Renvoie les commendes
   *
   * @return array
   */
  function getOrders(): array {
    return $this->orders;
  } 
  
  /**
   * Object pour stocker les données de 1 commande
   *
   * @return stdClass
   */
  private function getNewOrderStdClass(): stdClass {
    // orderStdClass
    $orderStdClass = new stdClass;

    $orderStdClass->brand = $this->inCommonInformations['brand'];
    $orderStdClass->orderId = $this->inCommonInformations['orderId'];    
    $orderStdClass->model = $this->inCommonInformations['model'];
    $orderStdClass->family = $this->inCommonInformations['family'];
    $orderStdClass->orderFrom = $this->inCommonInformations['orderFrom'];
    $orderStdClass->orderBuyer = $this->inCommonInformations['orderBuyer'];
    $orderStdClass->orderDate = $this->inCommonInformations['orderDate'];
    $orderStdClass->wipId = $this->inCommonInformations['wipId'];
    $orderStdClass->isValid = $this->inCommonInformations['isValid'];
    $orderStdClass->version = $this->inCommonInformations['version'];
    $orderStdClass->year = $this->inCommonInformations['year'];
    $orderStdClass->sorpDate = $this->inCommonInformations['sorpDate'];
    $orderStdClass->carName = $this->inCommonInformations['carName'];
    $orderStdClass->coverCode = '';
    $orderStdClass->deliveredDate = '';
    $orderStdClass->quantity = ''; 
    $orderStdClass->coverLink = '';    
    $orderStdClass->countryCode = ''; 
    $orderStdClass->countryName = '';     
    $orderStdClass->forecastPrint = 0;

    return $orderStdClass;
  }

  /**
   * Récupération des données communes aux commandes
   *
   * @return void
   */
  private function getInCommonInformations() {    

    // Récupération du Nom et Prénom
    $this->inCommonInformations['orderFrom'] = $this->user->getFistNameAndLastName();

    // Role de l'utilisateur
    $this->inCommonInformations['orderBuyer'] = $this->user->getFirstRole();

    // Id du fichier de commandes
    $this->inCommonInformations['orderId'] = uniqid();

    // Marque
    $brand = str_contains(strtolower($this->activeSheet->getCell('A2')->getValue()), 'opel') ? 
      'opel' :
      $this->activeSheet->getCell('A2')->getValue();

    $this->inCommonInformations['brand'] = $brand;

    // Model
    $this->inCommonInformations['model'] = $this->activeSheet->getCell('D2')->getValue();

    // carName
    $this->inCommonInformations['carName'] = $this->activeSheet->getCell('C2')->getValue();


    // Famille de la voiture
    $this->inCommonInformations['family'] = 'UNDEFINED';

    // Validité de la commande
    $this->inCommonInformations['isValid'] = true;

    // Date de lecture du fichier
    $this->inCommonInformations['orderDate'] = date('Y-m-d');

    // Statut initial des commandes présentent dans le fichier
    $this->inCommonInformations['wipId'] = StaticData::ORDER_STATUS['PREPARATION'];

    // Version
    $this->inCommonInformations['version'] = empty($this->activeSheet->getCell('E2')->getValue()) ?
      $this->getYearAndVersion('') :
      $this->getYearAndVersion($this->activeSheet->getCell('E2')->getValue())['version'];
   
    // Année
    $this->inCommonInformations['year'] =  empty($this->activeSheet->getCell('E2')->getValue()) ?
      $this->getYearAndVersion('') :
      $this->getYearAndVersion($this->activeSheet->getCell('E2')->getValue())['year'];
    
    // Date de référence sur le fichier
    $this->inCommonInformations['sorpDate'] = empty($this->activeSheet->getCell('A3')->getValue()) ?
      $this->getSorpDate('') :
      $this->getSorpDate($this->activeSheet->getCell('A3')->getValue());

    // Controle de la données
    foreach($this->inCommonInformations as $key=> $value) {
      if(empty($value)) {
        throw new \Exception('Some information missing on input file: '. $key);
      }
    }
  }

  /**
   * Récupération Date SORP
   *  
   * @param string $sorpText
   * @return string
   */
  private function getSorpDate(string $sorpText): string {
    // Suppression du mot sorp
    // Suppression des espaces 
    // remplace les . par des -
    // Suppression des espaces
    $sorpText = trim(preg_replace("/\s+/", "", strtolower($sorpText)));
    $sorpText = strval(str_replace('.', '-',trim(str_replace('sorp:','', strtolower($sorpText)))));
    
    // Format Date
    $sorpDate = \PHPExcel_Style_NumberFormat::toFormattedString($sorpText, 'YYYY-MM-DD');

    if(!isDateValid($sorpDate)){
      throw new \Exception('SORP Invalid date format', 400);
    }
    
    return $sorpDate;
  }

  /**
   * Récupération Année et Version du model
   *
   * @return array|null
   */
  private function getYearAndVersion(string $versionAndYearText): array {
    // Suppression des espaces
    $yearVersion = trim(preg_replace("/\s+/", "", strtolower($versionAndYearText)));

    // Données fichier invalide
    if(strlen($yearVersion) < 3) {
      throw new InvalidFormatException('Excel File not valid to determin Year and Version', 400);
    }

    // Année
    $year = substr($versionAndYearText, 0, 2);

    if(!is_numeric($year)) {
      throw new InvalidFormatException('Excel File not valid to determin Year and Version', 400);
    }

    // Version
    $version = strtoupper(substr($versionAndYearText, 2, 1));

    if(!is_string($version)) {
      throw new InvalidFormatException('Excel File not valid to determin Year and Version', 400);
    }

    return [
      'year' => $year,
      'version' => $version
    ];
  }
}