<?php
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/helpers/ExcelFileHelper.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/interfaces/OrderSourceInterface.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/model/Order.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/helpers/OrderHelper.php';

/**
 * Extraction données a partir d'un fichier Excel
 */
class OrderFromExcelFile extends ExcelFileHelper implements OrderSourceInterface {

  /**
   * Structure Document EXCEL
   *
   */
  const FILE_STRUCTURE = [
    'ROW_START' => 7,
    'COLUMN_START' => 1
  ]; 

  /**
   * Liste des commandes clients ectrait du fichier Xls
   *
   * @var array
   */
  protected array $orders = [];

  protected array $ordersBis = [];
  
  /**
   * OrderHelper
   *
   * @var OrderHelper
   */
  protected OrderHelper $orderHelper;

  function __construct(string $fileName, OrderHelper $orderHelper)
  { 
    // Constructeur parent
    parent::__construct($fileName); 
    $this->orderHelper = $orderHelper;
  }

  /**
   * Lecture du fichier contenant les commandes
   *
   * @return void
   */
  function readOrderSourceData(): void {
   
    // Vérification disponibilité PHPExcel
    $this->isPhpExcelAvailbale();

    // Récupération page Active des commandes
    $this->getActiveSheet();

    // Vérification du format du fichier
    $this->isOrderWorksheetReadable();

    // Parcours du fichier excel
    $this->browseOrderDataFile();
  }

  /**
   * Renvoie la liste des commandes
   *
   * @return array
   */
  function getOrders(): array {
    return $this->orders;
  }  

  /**
   * Renvoie la liste des commandes
   *
   * @return array
   */
  function getOrdersBis(): array {
    return $this->ordersBis;
  }  
 
  /**
   * Parcours du fichier commande
   *
   * @return void
   */
  private function browseOrderDataFile() {    
    // Récupération du site faisant la commande
    $orderBuyer = $this->activeSheet->getCell('A2')->getValue();       
    
    // Mise a jour de OrderBuyer
    $this->orderHelper->setOrderBuyer($orderBuyer);

    // Derniere ligne
    $LAST_ROW = $this->activeSheet->getHighestRow();

    // Derniere colonne
    $LAST_COLUMN = $this->activeSheet->getHighestDataColumn();
    $COLUMN_COUNT_NUMBER = PHPExcel_Cell::columnIndexFromString($LAST_COLUMN);    
   
    for($row = self::FILE_STRUCTURE['ROW_START']; $row < $LAST_ROW; $row++) {
      
      // Nouveau OrderStdClass
      $orderStdClass = $this->orderHelper->getNewOrderStdClass();
      
      for($col = self::FILE_STRUCTURE['COLUMN_START']; $col <= $COLUMN_COUNT_NUMBER; $col++ ) {
        
        $this->completeOrderStdClass($orderStdClass, $row, ($col - 1));        
      }
      
      if(!empty($orderStdClass->partNumber)) {
        $this->orders[] = $this->createOrder($orderStdClass);
        $this->ordersBis[] = $this->createOrderClassFormat($orderStdClass);
      }      
    }
  }

  /**
   * Complete le Order StdClass à partir du fichier XLS et 
   *
   * @param stdClass $carData
   * @param integer $row - Ligne fuchier xls
   * @param integer $col - Colonne fichier xls
   * @param OrderHelper $orderHelper - Helper pour aider à la création d'une nouvlle commande
   * @return void
   */
  private function completeOrderStdClass(stdClass &$orderStdClass, int $row, int $col) {

    switch($col) {
      // Code pochette
      case 0: 
        $orderStdClass->coverCode = $this->activeSheet->getCell(PHPExcel_Cell::stringFromColumnIndex($col).$row)->getValue();
        break;
      // PartNumber
      case 1: 
        $orderStdClass->partNumber = $this->activeSheet->getCell(PHPExcel_Cell::stringFromColumnIndex($col).$row)->getValue();
        if(!empty($orderStdClass->partNumber)) {          
          // Récupération des données pays
          $countryData = $this->orderHelper->getCountryInformation($orderStdClass->partNumber);
          $orderStdClass->countryCode = $countryData['countryCode'];
          $orderStdClass->countryName = $countryData['country'];

          // Link impression
          $orderStdClass->coverLink = $this->orderHelper->getCoverLink($orderStdClass->partNumber);
        }
        break;

      // Model
      case 2: 
        $orderStdClass->model = $this->activeSheet->getCell(PHPExcel_Cell::stringFromColumnIndex($col).$row)->getValue();
        break;

      // Quantité
      case 3:
        $orderStdClass->quantity = $this->activeSheet->getCell(PHPExcel_Cell::stringFromColumnIndex($col).$row)->getValue();
        break;
      
      // Date      
      case 4:        
        $date = $this->activeSheet->getCell(PHPExcel_Cell::stringFromColumnIndex($col).$row)->getValue();
        $orderStdClass->deliveredDate = \PHPExcel_Style_NumberFormat::toFormattedString($date, 'YYYY-MM-DD');
        break;

      default:  ;break;
    }
  }

  /**
   * Renvoie un nouvel Objet Order
   *
   * @param \stdClass $carData
   * @return Order
   */
  private function createOrder(\stdClass $orderStdClass): Order {
    $order = new Order(
      $orderStdClass->orderId,
      $orderStdClass->coverCode,
      $orderStdClass->model,
      $orderStdClass->family,
      $orderStdClass->orderFrom,
      $orderStdClass->orderBuyer,
      $orderStdClass->deliveredDate,
      $orderStdClass->quantity,
      $orderStdClass->partNumber,
      $orderStdClass->coverLink,
      $orderStdClass->orderDate,
      $orderStdClass->countryCode,
      $orderStdClass->countryName,
      $orderStdClass->wip
    );
   
    return $order;
  }

  /**
   * 
   */
  private function createOrderClassFormat(\stdClass $orderStdClass): array {
    return [
      'orderId' => $orderStdClass->orderId,
      'coverCode' => $orderStdClass->coverCode,
      'model' => $orderStdClass->model,
      'family' => $orderStdClass->family,
      'orderFrom' => $orderStdClass->orderFrom,
      'orderBuyer' => $orderStdClass->orderBuyer,
      'deliveredDate' => $orderStdClass->deliveredDate,
      'quantity' => $orderStdClass->quantity,
      'partNumber' => $orderStdClass->partNumber,
      'coverLink' => $orderStdClass->coverLink,
      'orderDate' => $orderStdClass->orderDate,
      'countryCode' => $orderStdClass->countryCode,
      'countryName' => $orderStdClass->countryName,
      'wip' => $orderStdClass->wip
    ];
  }
}
