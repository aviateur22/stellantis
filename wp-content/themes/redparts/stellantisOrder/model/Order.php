<?php
/**
 * Model Order
 */
class Order {  
  /**
   * Réference commande
   *
   * @var string
   */
  protected string $orderId;

  /**
   * Date de prise de commande
   * 
   * @var string
   */
  protected string $orderDate;

  /**
   * Code pays
   *
   * @var string
   */
  protected string $countryCode;

  /**
   * Nom pays 
   * 
   * @var string
   */
  protected string $countryName;

  /**
   * Code pochette
   *
   * @var string
   */
  protected string $coverCode;

  /**
   * Liens du document a imprimer
   *
   * @var string
   */
  protected string $coverLink;

  /**
   * PartNumber
   *
   * @var string
   */
  protected string $partNumber;

  /**
   * Quantité
   *
   * @var string
   */
  protected string $quantity;

  /**
   * Date expédition commande
   *
   * @var string
   */
  protected string $deliveredDate;

  /**
   * Statut de la commande
   *
   * @var string
   */
  protected int $wipId;

  /**
   * Site de destination
   * Fichier XLS - Cellule A2
   * @var string
   */
  protected string $orderBuyer;

  /**
   * Compte ayant fait la commande
   * WP-get-user()
   * @var string
   */
  protected string $orderFrom;
  
  /**
   * Voiture
   * A CFM - Table de Correspondance ?
   * @var string
   */
  protected string $family;

  /**
   * Marque commande
   *
   * @var string
   */
  protected string $brand;

  /**
   * Modele de voiture
   * Fichier XLS - Carlinename
   * @var string
   */
  protected string $model;

  /**
   * Commande valide
   *
   * @var bool
   */
  protected bool $isValid;

  /**
   * Année
   *
   * @var integer
   */
  protected int $year;

  /**
   * Version
   *
   * @var string
   */
  protected string $version;

  /**
   * Prévison d'impression
   *
   * @var integer
   */
  protected int $printForecast;

  /**
   * documentationPDF
   *
   * @var array
   */
  protected array $PDFdocumentations;

  /**
   * LanguageCode
   *
   * @var string
   */
  protected string $languageCode;

  /**
   * Nom du vehicule
   *
   * @var string
   */
  protected string $carName;


  function __construct(
    string $orderId,
    string $coverCode,
    string $model,
    string $family,
    string $orderFrom,
    string $orderBuyer,
    string $deliveredDate,
    string $quantity,
    string $partNumber,
    string $coverLink,
    string $orderDate,
    string $countryCode,
    string $countryName,
    int $wipId,
    bool $isValid,
    string $brand,
    string $version,
    int $year,
    int $printForecast,
    array $PDFdocumentations,
    string $languageCode,
    string $carName
  ) {
    $this->coverCode = $coverCode;
    $this->model = $model;
    $this->family = $family;
    $this->orderFrom = $orderFrom;
    $this->deliveredDate = $deliveredDate;
    $this->quantity = $quantity;
    $this->partNumber = $partNumber;
    $this->orderId = $orderId;
    $this->orderBuyer = $orderBuyer;
    $this->coverLink = $coverLink;
    $this->orderDate = $orderDate;
    $this->countryCode = $countryCode;
    $this->countryName = $countryName;
    $this->wipId = $wipId;
    $this->isValid = $isValid;
    $this->brand = $brand;
    $this->version = $version;
    $this->year = $year;
    $this->printForecast = $printForecast;
    $this->PDFdocumentations = $PDFdocumentations;
    $this->languageCode = $languageCode;
    $this->carName = $carName;
  }
  #Region Getter

    function getModel(): string {
      return $this->model;
    }

    function getOrderform(): string {
      return $this->orderFrom;
    }

    /**
     * Renvoie id de la commande
     *
     * @return string
     */
    function getOrderId(): string {
      return $this->orderId;
    }

    /**
     * Renvoie le schedule date
     *
     * @return string
     */
    function getdeliveredDate(): string {
      return $this->deliveredDate;
    }

    /**
     * Renvoie la famille 
     *
     * @return string
     */
    function getFamily(): string {
      return $this->family;
    }

    /**
     * Renvoie OrderBuyer
     *
     * @return string
     */
    function getOrderBuyer(): string {
      return $this->orderBuyer;
    }

    /**
     * Renvoie la quantité
     *
     * @return string
     */
    function getQuantity(): string {
      return $this->quantity;
    }

    /**
     * Renvoie le partNumber
     *
     * @return string
     */
    function getPartNumber(): string {
      return $this->partNumber;
    }

    /**
     * Renvoi le code pochette
     *
     * @return string
     */
    function getCoverCode(): string {
      return $this->coverCode;
    }

    /**
     * Code pays
     *
     * @return string
     */
    function getCountryCode(): string {
      return $this->countryCode;
      
    }

    /**
     * Nom pays
     *
     * @return string
     */
    function getCountryName(): string {
      return $this->countryName;
    }

    /**
     * Date de commande
     *
     * @return string
     */
    function getOrderDate(): string {
      return $this->orderDate;
    }

    /**
     * ¨Statut commande
     *
     * @return string
     */
    function getWipId(): int {
      return $this->wipId;
    }

    /**
     * Lien document d'impression
     *
     * @return string
     */
    function getCoverLink(): string {
      return $this->coverLink;
    }

    /**
     * Renvoie isValid
     *
     * @return boolean
     */
    function getIsValid(): bool {
      return $this->isValid;
    }

    /**
     * Renvoie la marque
     *
     * @return string
     */
    function getBrand(): string {
      return $this->brand;
    }

    /**
     * Renvoie la version
     *
     * @return string
     */
    function getVersion(): string {
      return $this->version;
    }

    /**
     * Renvoie l'année
     *
     * @return integer
     */
    function getYear(): int {
      return $this->year;
    }

    /**
     * Renvoi le nombre d'impression
     *
     * @return integer
     */
    function getPrintForecast(): int {
      return $this->printForecast;
    }

    /**
     * infoDocPdf
     *
     * @return array
     */
    function getPdfDocumentations(): array {
      return $this->PDFdocumentations;
    }

    /**
     * Renvoie le language code
     *
     * @return string
     */
    function getLanguageCode(): string {
      return $this->languageCode;
    }
 
    /**
     * Renvoie le nom de la voiture
     *
     * @return string
     */
    function getCarName(): string {
      return $this->carName;
    }

  #endRegion
}