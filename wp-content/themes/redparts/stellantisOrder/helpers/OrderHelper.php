<?php
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/utils/StaticData.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/services/MySqlModelRepository.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/interfaces/OrderRepositoryInterface.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/utils/validators.php';
require_once('/home/mdwfrkglvc/www/wp-config.php');

/**
 * Helper pour construction d'un nouvelle Commande
 */
class OrderHelper {

  /**
   * OrderId
   *
   * @var string
   */
  protected string $orderId;

  /**
   * Date de commande
   *
   * @var string
   */
  protected string $orderDate;

  /**
   * OrderBuyer
   *
   * @var string
   */
  protected string $orderBuyer;

  /**
   * Marque voiture
   *
   * @var string
   */
  protected string $brand;

  /**
   * Liste commandes en echec
   *
   * @var array
   */
  protected array $failureOrders = [];

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
  protected array $errorOnQuantityOrders = [];

  /**
   * Personne faisant la commande
   *
   * @var string
   */
  protected string $orderFrom;

   /**
   * Orders Repository
   *
   * @var OrderRepositoryInterface
   */
  protected OrderRepositoryInterface $orderRepository;

  /**
   * Undocumented variable
   *
   * @var ModelRepositoryInterface
   */
  protected ModelRepositoryInterface $modelRespository;


  function __construct(OrderRepositoryInterface $orderRepository, ModelRepositoryInterface $modelRepository)
  {
    $this->orderRepository = $orderRepository;
    $this->modelRespository = $modelRepository;
    $this->orderDate = date('y-m-d');
    $this->orderId = uniqid();
  }

  /**
   * Initilaisalstion propriété des commandes
   *
   * @return stdClass
   */
  function getNewOrderStdClass(): stdClass {
    $orderStdClass = new stdClass;
    $orderStdClass->brand = $this->brand;
    $orderStdClass->orderId = $this->orderId; 
    $orderStdClass->coverCode = ''; 
    $orderStdClass->model = ''; 
    $orderStdClass->family = ''; 
    $orderStdClass->orderFrom = $this->orderFrom; 
    $orderStdClass->orderBuyer = $this->orderBuyer; 
    $orderStdClass->deliveredDate = ''; 
    $orderStdClass->quantity = ''; 
    $orderStdClass->partNumber = ''; 
    $orderStdClass->coverLink = ''; 
    $orderStdClass->orderDate = $this->orderDate; 
    $orderStdClass->countryCode = ''; 
    $orderStdClass->countryName = ''; 
    $orderStdClass->wipId = StaticData::ORDER_STATUS['PREPARATION'];
    $orderStdClass->isValid = true;
    $orderStdClass->version = '';
    $orderStdClass->year = 0;

    return $orderStdClass;
  }
  
  /**
   * Vérification du partNumber
   *
   * @param string|null $partNumber
   * @return boolean
   */
  function isPartNumberValid($partNumber): bool {    
    $pattern = "/[0-9][0-9][a-zA-Z][a-zA-Z][a-zA-Z][a-zA-Z][a-zA-Z\d+]*/"; 
    if(empty($partNumber) || !preg_match($pattern, $partNumber)) {
      return false;
    }
    return true;
  }

  /**
   * MAJ de la marque
   *
   * @param string $brand
   * @return void
   */
  function setBrand(string $brand): void {
    $this->brand = $brand;
  }

  /**
   * MAJ du orderBuyer
   *
   * @param string $orderBuyer 
   * @return void
   */
  function setOrderBuyer(string $orderBuyer): void {
    $this->orderBuyer = $orderBuyer;
  }

  /**
   * MAJ de orderFrom
   *
   * @param string $orderFrom
   * @return void
   */
  function setOrderFrom(string $orderFrom): void {
    $this->orderFrom =$orderFrom;
  }

  /**
   * Récupération des données du pays
   *
   * @param string $partNumber
   * @return array
   */
  public function getCountryInformation(string $partNumber): array {
    return [
      'country' => 'FRANCE',
      'countryCode' => 'FR'
    ];
  }

  /**
   * Récupération des données de liens d'impression
   *
   * @param string $partNumber
   * @return string
   */
  public function getCoverLink(string $partNumber): string {
    // Lien documentation trouvé
    $isCoverLinkFind = $this->isCoverLinkFind($partNumber);

    if(!$isCoverLinkFind) {
      $this->failureOrders[] = $partNumber;
      return '';
    }

    return 'www-link/'. $partNumber .'/impression/document/'. $partNumber;
  }

  /**
   * Renvoie la liste des commandes en echec
   *
   * @return array
   */
  public function getFailureOrders(): array {
    return $this->failureOrders;
  }

  /**
   * Renvoie la liste des commanded duplisuées
   *
   * @return array
   */
  public function getDuplicateOrders(): array {
    return $this->duplicateOrders;
  }

  /**
   * Renvoie la liste des Commandes avec une quantité = 0
   *
   * @return array
   */
  public function getErrorQuantityOrders(): array {
    return $this->errorOnQuantityOrders;
  }

  /**
   * Vérification des commandes en double
   *
   * @param string $partNumber
   * @param string $deliveredDate
   * @return void
   */
  public function isOrderDuplicate(string $partNumber, string $deliveredDate): bool {
    $duplicateOrder = $this->orderRepository->findOneDuplicatedOrder($partNumber, $deliveredDate);   
    
    // Commande dupliqué
    if(count($duplicateOrder) > 0) {
      $this->duplicateOrders[] = $partNumber;
      return true;
    }
    return false;
  }

  /**
   * Ajout d'une commande en erreur de quantité
   *
   * @param string $partNumber
   * @return void
   */
  public function addOrderToErrorList(string $partNumber) {
    $this->errorOnQuantityOrders[] = $partNumber;
  }

  /**
   * Renvoi la famille
   *
   * @return void
   */
  public function getFamily() {

  }
  /**
   * Vérification validité model
   *
   * @param string $partNumber
   * @param string $modelOnOrder
   * @return boolean
   */
  public function isModelValid(string $partNumber, string $modelOnOrder): bool {
    $modelCode = strtolower(substr($partNumber, 3, 4));
    
    $model = $this->modelRespository->findOneByCode($modelCode);
    
    if(empty($model)) {
      throw new \Exception('Non-exsitent model ' . $modelCode, 400);
    }

    if($model['model'] === $modelOnOrder) {
      throw new \Exception('Discrepency between model order and database ' . $modelCode, 400);
    }
    return true;
  }

  /**
   * Renvoie le modèle
   *
   * @return void
   */
  public function getModel() {

  }

  /**
   * Recherche du lien d'impression de la documentation
   *
   * @return boolean
   */
  private function isCoverLinkFind():bool {
    return true;
  }

  private function setYear(string $partNumber) {

  }

  private function setVersion(string $partNumber) {

  }
}