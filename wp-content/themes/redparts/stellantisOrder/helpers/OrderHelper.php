<?php
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
   * Liste commandes en echec
   *
   * @var array
   */
  protected array $failureOrders = [];

  /**
   * Personne faisant la commande
   *
   * @var string
   */
  protected string $orderFrom;

  protected 

  function __construct()
  {
    $this->orderDate = date('y-m-d');
    $this->orderFrom = $this->getOrderFrom();
    $this->orderId = uniqid();
  }

  /**
   * Initilaisalstion propriété des commandes
   *
   * @return stdClass
   */
  function getNewOrderStdClass(): stdClass {
    $orderStdClass = new stdClass;
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
    $orderStdClass->wip = 'PREPARATION';

    return $orderStdClass;
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
   * Renvoi la famille
   *
   * @return void
   */
  public function getFamily() {

  }

  /**
   * Renvoie le modèle
   *
   * @return void
   */
  public function getModel() {

  }
  
  /**
   * Récupération identifiant dela personne faisant la commande
   *
   * @return string
   */
  private function getOrderFrom(): string {
    $user_info = wp_get_current_user();
    return $user_info->first_name . " " . $user_info->last_name;
  }

  /**
   * Recherche du lien d'impression de la documentation
   *
   * @return boolean
   */
  private function isCoverLinkFind():bool {
    return true;
  }


}