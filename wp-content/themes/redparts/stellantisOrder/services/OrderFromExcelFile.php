<?php
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/helpers/ExcelFileHelper.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/interfaces/OrderSourceInterface.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/model/Order.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/helpers/OrderHelper.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/model/User.php';

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
  
  /**
   * OrderHelper
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

  function __construct(string $fileName, OrderHelper $orderHelper, User $user)
  { 
    // Constructeur parent
    parent::__construct($fileName); 
    $this->orderHelper = $orderHelper;
    $this->user = $user;
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
   * Parcours du fichier commande
   *
   * @return void
   */
  private function browseOrderDataFile() {    
    // Récupération du site faisant la commande    
    $orderBuyer = $this->user->getFirstRole();
    
    // Récupération de la marque de  la voiture
    $brand = $this->activeSheet->getCell('A2')->getValue();

    // Personne faisant la commande
    $orderFrom = $this->user->getFistNameAndLastName();
    
    // Mise a jour de OrderBuyer
    $this->orderHelper->setOrderBuyer($orderBuyer);

    // Mise a jour de la marque
    $this->orderHelper->setBrand($brand);

    // Mise a jour de la personne faisoant la commande
    $this->orderHelper->setOrderFrom($orderFrom);

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
      
      // Ajout à la liste des commandes
      if($this->orderHelper->isPartNumberValid($orderStdClass->partNumber)) {
        $this->orders[] = $this->createOrder($orderStdClass);
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
        $cellValue = $this->activeSheet->getCell(PHPExcel_Cell::stringFromColumnIndex($col).$row)->getValue();

        if(!$this->orderHelper->isPartNumberValid($cellValue)) {
          return;
        }
        // PartNumber
        $orderStdClass->partNumber = $cellValue;

        // Récupération des données pays
        $countryData = $this->orderHelper->getCountryInformation($orderStdClass->partNumber);
        $orderStdClass->countryCode = $countryData['countryCode'];
        $orderStdClass->countryName = $countryData['country'];

        // Link doc PDF
        $coverLink = $this->orderHelper->getCoverLink($orderStdClass->partNumber);        

        if(empty($coverLink)) {           
          $orderStdClass->isValid = false;
          $orderStdClass->coverLink = '';

        } else {
          $orderStdClass->coverLink = $coverLink;
        }

        // Version 
        $version = substr($orderStdClass->partNumber, 2, 1);
        $orderStdClass->version = $version;
        if(!is_string($version)) {
          $this->orderHelper->addOrderToErrorList($orderStdClass->partNumber);
          $orderStdClass->isValid = false;
        }

        // Année
        $year = substr($orderStdClass->partNumber, 0, 2);
        $orderStdClass->year = $year;
        if(!is_numeric($year)) {
          $this->orderHelper->addOrderToErrorList($orderStdClass->partNumber);
          $orderStdClass->isValid = false;
        }
        break;

      // Model
      case 2:

        // Si partNumber valide
        if(!$this->orderHelper->isPartNumberValid($orderStdClass->partNumber)) {
          return;
        }

        // Model de la voiture
        $orderStdClass->model = $this->activeSheet->getCell(PHPExcel_Cell::stringFromColumnIndex($col).$row)->getValue();

        // Vérification cohérence Model
        $this->orderHelper->isModelValid($orderStdClass->partNumber,  $orderStdClass->model);
        break;

      // Quantité
      case 3:

        // Si partNumber valide
        if(!$this->orderHelper->isPartNumberValid($orderStdClass->partNumber)) {
          return;
        }

        $orderStdClass->quantity = $this->activeSheet->getCell(PHPExcel_Cell::stringFromColumnIndex($col).$row)->getValue();

        // Si quantité = 0        
        if(strval($orderStdClass->quantity) === '0' || !is_numeric($orderStdClass->quantity)) {          
          $this->orderHelper->addOrderToErrorList($orderStdClass->partNumber);
          $orderStdClass->isValid = false;
        }
        break;
      
      // Date      
      case 4: 

        // Si partNumber valide
        if(!$this->orderHelper->isPartNumberValid($orderStdClass->partNumber)) {
          return;
        }

        $date = $this->activeSheet->getCell(PHPExcel_Cell::stringFromColumnIndex($col).$row)->getValue();
        $orderStdClass->deliveredDate = \PHPExcel_Style_NumberFormat::toFormattedString($date, 'YYYY-MM-DD');

        // Vérification si commande déja existante        
        $isOrderDuplicate = $this->orderHelper->isOrderDuplicate($orderStdClass->partNumber, $orderStdClass->deliveredDate);

        // Commande dupliquée
        if($isOrderDuplicate) {
          $orderStdClass->isValid = false;
        }
        break;
        

      default:  
      break;
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
      $orderStdClass->wipId,
      $orderStdClass->isValid,
      $orderStdClass->brand,
      $orderStdClass->version,
      $orderStdClass->year
    );   
    return $order;
  }
}
