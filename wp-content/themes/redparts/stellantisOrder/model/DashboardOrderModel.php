<?php
require_once ('/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/model/OrderEntity.php');
/**
 * modèle OrderDashboard
 */
class DashboardOrderModel {
  /**
   * PartNumber
   *
   * @var string
   */
  protected string $partNumber;  
  
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
   * Voiture
   * A CFM - Table de Correspondance ?
   * @var string
   */
  protected string $family;

  /**
   * Modele de voiture
   * Fichier XLS - Carlinename
   * @var string
   */
  protected string $model;

  /**
   * Liste de quantité par jour 
   *
   * @var array
   */
  protected array $quantitiesByDate; 

  function __construct(        
    string $coverCode,
    string $model,
    string $family,    
    string $partNumber,
    string $coverLink,
    string $countryCode,
    string $countryName,
    array $quantitiesByDate
    ) {
    $this->coverCode = $coverCode;
    $this->model = $model;
    $this->family = $family;        
    $this->partNumber = $partNumber;    
    $this->coverLink = $coverLink;    
    $this->countryCode = $countryCode;
    $this->countryName = $countryName;    
    $this->quantitiesByDate = $quantitiesByDate;
  }
  #Region Getter

  function getModel(): string {
    return $this->model;
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
   * Lien document d'impression
   *
   * @return string
   */
  function getCoverLink(): string {
    return $this->coverLink;
  }

   /**
   * Renvoie les quantités par date
   *
   * @return array
   */
  function getQuantitiesByDate(): array {
    return $this->quantitiesByDate;
  }

#endRegion
}