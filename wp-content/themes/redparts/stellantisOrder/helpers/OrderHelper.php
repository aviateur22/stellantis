<?php
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/utils/validators.php';

/**
 * Undocumented class
 */
class OrderHelper {

  /**
   * Object contenant les données de 1 commande
   *
   * @var stdClass
   */
  protected stdClass $orderStdClass;

  /**
   * Model repository
   *
   * @var ModelRepositoryInterface
   */
  protected ModelRepositoryInterface $modelRepository;

  /**
   * Order repository
   *
   * @var OrderRepositoryInterface
   */
  protected OrderRepositoryInterface $orderRepository;

  /**
   * Liste commandes en echec
   *
   * @var array
   */
  protected array $missingCoverLinkOrders = [];

  /**
   * Liste commandes en echec
   *
   * @var array
   */
  protected array $duplicateOrders = [];

  /**
   * Lite des commandes avec des quantités en erreur
   *
   * @var array
   */
  protected array $otherErrorOnOrders = [];

  /**
   * Prévision impression Commande sur les prochaines semaines
   *
   * @var ForecastPrintHelper
   */
  protected ForecastPrintHelper $forecastPrintHelper;

  function __construct(
    OrderRepositoryInterface $orderRepository,
    ModelRepositoryInterface $modelRepository,
    ForecastPrintHelper $forecastPrintHelper    
    ) {    
    $this->modelRepository = $modelRepository;
    $this->orderRepository = $orderRepository;
    $this->forecastPrintHelper = $forecastPrintHelper;
  }

  /**
   * Validation des données de la commandes
   *
   * @param stdClass $orderStdClass
   * @return void
   */
  function areOrderPropertiesValid(stdClass $orderStdClass) {

    $this->orderStdClass = $orderStdClass;  
    
    $this->isPartNumberValid();
    $this->isDeliveredDateValid();
    $this->isOrderDuplicated();
    $this->isModelValid();
    $this->isFamilyValid();
    $this->isQuantityValid();
    $this->isYearValid();
    $this->isVersionValid();
    

    // Récupération des données manquantes à partir du PartNumber
    $this->orderStdClass->coverLink = $this->findCoverLink(); 
    $CountryInfo = $this->getCountryInformation();
    $this->orderStdClass->countryName = $CountryInfo['country'];
    $this->orderStdClass->countryCode = $CountryInfo['countryCode'];
    $this->orderStdClass->forecastPrint = $this->getForecastPrint();
    $this->formatDeliveredDate();
  }  

  /**
   * Renvoie un nouvel Objet Order
   *
   * @param \stdClass $carData
   * @return Order
   */
  function createOrder(): Order {  
    $order = new Order(
      $this->orderStdClass->orderId,
      $this->orderStdClass->coverCode,
      $this->orderStdClass->model,
      $this->orderStdClass->family,
      $this->orderStdClass->orderFrom,
      $this->orderStdClass->orderBuyer,
      $this->orderStdClass->deliveredDate,
      $this->orderStdClass->quantity,
      $this->orderStdClass->partNumber,
      $this->orderStdClass->coverLink,
      $this->orderStdClass->orderDate,
      $this->orderStdClass->countryCode,
      $this->orderStdClass->countryName,
      $this->orderStdClass->wipId,
      $this->orderStdClass->isValid,
      $this->orderStdClass->brand,
      $this->orderStdClass->version,
      $this->orderStdClass->year,
      $this->orderStdClass->forecastPrint
    );   
    return $order;
  }

  /**
   * Renvoie les commandes dupliquées
   *
   * @return array
   */
  function getDuplicateOrders(): array {
    return $this->duplicateOrders;
  }

  /**
   * Renvoie les commandes avec liens documentation PFD manquant
   *
   * @return array
   */
  function getMissingCoverLinkOrders(): array {
    return $this->missingCoverLinkOrders;
  }

  /**
   * Renvoie les commandes avec d'autres erreurs
   *
   * @return array
   */
  function getOtherErrorOrders(): array {
    return $this->otherErrorOnOrders;
  }
  
  /**
   * Vérification du PartNumber
   *
   * @return boolean
   */
  private function isPartNumberValid(): bool {
    $pattern = "/[0-9][0-9][a-zA-Z][a-zA-Z][a-zA-Z][a-zA-Z][a-zA-Z\d+]*/"; 
    if(empty($this->orderStdClass->partNumber) || !preg_match($pattern, $this->orderStdClass->partNumber)) {
      $this->otherErrorOnOrders[] = $this->orderStdClass->partNumber;  
      $this->orderStdClass->isValid = false; 

      return false;
    }
    return true;
  }

  /**
   * Vérification si PartNumber deja existant au méme date
   *
   * @return boolean
   */
  private function isOrderDuplicated(): bool {
    $duplicateOrder = $this->orderRepository->findOneDuplicatedOrder($this->orderStdClass->partNumber, $this->orderStdClass->deliveredDate, $this->orderStdClass->orderBuyer);  
    
    // Commande dupliqué
    if(count($duplicateOrder) > 0) {
      $this->duplicateOrders[] = $this->orderStdClass->partNumber;
      $this->orderStdClass->isValid = false;
      return true;
    }
    return false;
  }
  
  /**
   * Vérification du Model
   *
   * @return boolean
   */
  private function isModelValid(): bool {    

    $modelCode = strtolower(substr($this->orderStdClass->partNumber, 3, 4));
    
    $model = $this->modelRepository->findOneByCode($modelCode);  
    
    // Aucun model present en base de données
    if(empty($model)) {
      $this->otherErrorOnOrders[] = $this->orderStdClass->partNumber;  
      $this->orderStdClass->isValid = false;      
      return false;
    }

    // Vérification du model Code
    if(strtolower($model['code']) !== strtolower($this->orderStdClass->model)) {      
      $this->otherErrorOnOrders[] = $this->orderStdClass->partNumber;
      $this->orderStdClass->isValid = false;
      return false;
    }

    // Vérification du model name
    if(strtolower($model['model']) !== strtolower($this->orderStdClass->modelName)) {
      $this->otherErrorOnOrders[] = $this->orderStdClass->partNumber;
      $this->orderStdClass->isValid = false;
      return false;
    }

    return true;
  } 

  /**
   * Vérification de la version
   *
   * @return boolean
   */
  private function isVersionValid(): bool {
    $version = strtolower(substr($this->orderStdClass->partNumber, 2, 1));

    if($version !== strtolower($this->orderStdClass->version) || !is_string($this->orderStdClass->version)) {
      $this->otherErrorOnOrders[] = $this->orderStdClass->partNumber;
      $this->orderStdClass->isValid = false;
      return false;
    }

    return true;
  }

  /**
   * Vérification de l'année
   *
   * @return boolean
   */
  private function isYearValid(): bool {
    $year = strtolower(substr($this->orderStdClass->partNumber, 0, 2));

    if($year !== $this->orderStdClass->year || !is_numeric($this->orderStdClass->year)) {      
      $this->otherErrorOnOrders[] = $this->orderStdClass->partNumber;
      $this->orderStdClass->isValid = false;
      return false;
    }

    return true;
  }

  /**
   * Vérification de la Familly
   *
   * @return boolean
   */
  private function isFamilyValid():bool {
    return true;
  }  

  /**
   * Vérification de la quantité
   *
   * @return boolean
   */
  private function isQuantityValid():bool {
    // Si quantité = 0 ou pas numérique     
    if(strval($this->orderStdClass->quantity) === '0' || !is_numeric($this->orderStdClass->quantity)) {             
      $this->otherErrorOnOrders[] = $this->orderStdClass->partNumber;
      $this->orderStdClass->partNumber;
      $this->orderStdClass->isValid = false;
      return false;
    }
    return true;
  }

  /**
   * Vérification de la Documentation PDF
   *
   * @return string
   */
  private function findCoverLink(): string {
    // Lien documentation trouvé
    $coverLinkFind = $this->getCoverLink($this->orderStdClass->partNumber);

    if(empty($coverLinkFind)) {     
      $this->missingCoverLinkOrders[] = $this->orderStdClass->partNumber;
      $this->orderStdClass->isValid = false;
      return '';
    }

    return $coverLinkFind;
  }

  /**
   * Vérification de la date de livraison
   *
   * @return boolean
   */
  private function isDeliveredDateValid(): bool{    
    // Controle de la date de livraison
    if(empty($this->orderStdClass->deliveredDate)) {
      $this->getDeliveredDateFromSorpDate();
    }

    $isDateValid = isDateValid($this->orderStdClass->deliveredDate);

    if(!$isDateValid) {     
      $this->otherErrorOnOrders[] = $this->orderStdClass->partNumber;
      $this->orderStdClass->isValid = false;;
      throw new Exception('DeliveredDate Unvalid, Check input file', 400);
      return false;
    }
    return true;
  }

  /**
   * Formate la date de livraison
   *
   * @return void
   */
  private function formatDeliveredDate(): void {
    $this->orderStdClass->deliveredDate = date('d-M-Y', strtotime($this->orderStdClass->deliveredDate));
  }

  /**
   * Détermination date de livraison si deliveredDate non définie
   *
   * @return void
   */
  private function getDeliveredDateFromSorpDate() {
    // Nombre de jour à déduire sur le SORP date
    $daysToRemove = '-' . StaticData::NUMBER_DAY_REMOVE_ON_SORP_DATE . ' days';
    
    // Jour de début
    $this->orderStdClass->deliveredDate = date('Y-m-d', strtotime($daysToRemove, strtotime($this->orderStdClass->sorpDate)));
  }

  /**
   * Récupération info Pays
   *
   * @return array
   */
  private function getCountryInformation(): array {
    return [
      'country' => 'UNDEFINED',
      'countryCode' => 'UNDEFINED'
    ];
  }
  
  /**
   * Renvoie le liens docuementation PDF
   *
   * @return string|null
   */
  private function getCoverLink(): string {
    return 'www-link/'. $this->orderStdClass->partNumber .'/impression/document/'. $this->orderStdClass->partNumber;
  }

  /**
   * Renvoie la prévision d'impression
   *
   * @return int
   */
  private function getForecastPrint(): int {    
    $printForecast = $this->forecastPrintHelper->getForecastOrdersQuantity($this->orderStdClass->deliveredDate, $this->orderStdClass->partNumber);
    return $printForecast;
  }  
}