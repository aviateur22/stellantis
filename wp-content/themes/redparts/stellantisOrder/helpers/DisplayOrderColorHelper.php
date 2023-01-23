<?php
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/utils/StaticData.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/utils/validators.php';

/**
 * Helper pour récupérer la couleur d'une commande
 */
class DisplayOrderColorHelper {

  /**
   * Utilisateur
   *
   * @var User
   */
  protected User $user;

  /**
   * Statut de la commande
   * 
   * @var int
   */
  protected int $wipId;

  function __construct(User $user) {
    $this->user = $user;
  }

  /**
   * recherche className affichant la couleur d'un commande
   *
   * @param int $wipId
   * @return string
   */
  function checkUserRole(int $wipId): string {
    // WipId
    $this->wipId = $wipId;

    // Role Millau
    if(isUserRoleFind($this->user, StaticData::MILLAU_FACTORY_ROLE_NAME)) {
      return $this->userRoleCustomOrderColor(StaticData::MILLAU_ID);

    } elseif(isUserRoleFind($this->user, StaticData::MANCHECOURT_FACTORY_ROLE_NAME)) {
      // Role Manchecourt
      return $this->userRoleCustomOrderColor(StaticData::MANCHECOURT_ID);

    } elseif(isUserRoleFind($this->user, StaticData::STELLANTIS_ROLE_NAME)) {
      // Role stellantis
      return $this->userRoleCustomOrderColor(StaticData::STELLANTIS_ID);

    } else {
      // Autre role
      return $this->findColorOrderDisplay();
    }
  }

  /**
   * Rercherche classname
   *
   * @param array $factoryWipIdArray - Liste des statusId de l'usine
   * @return string - ClassName
   */
  private function userRoleCustomOrderColor(array $factoryWipIdArray): string {
    // Si commande avec statut bloqué
    if(in_array($this->wipId, StaticData::BLOCKED_ID)) {
      return StaticData::CLASS_NAME_ORDERS_COLORS['BLOCKED_CLASS_NAME'];
    }
    
    // Suppression id = blocked qui est en derniere position du tableau
    $removedLastElement = array_pop($factoryWipIdArray);
    
    if($this->wipId > max($factoryWipIdArray)) {
      // Si wipId supérieur à factoryWipIdArray
      return StaticData::CLASS_NAME_ORDERS_COLORS['DELIVERED_CLASS_NAME'];

    } elseif($this->wipId < min($factoryWipIdArray)) {
      // Ajout clignotement si WipId === delivered
      $blinkingClassName = $this->isBlinking($factoryWipIdArray);
      
      // Si wipId inférieur à factoryWipIdArray
      $orderClassName = StaticData::CLASS_NAME_ORDERS_COLORS['PREFLIGHT_CLASS_NAME'];

      return (trim($blinkingClassName . ' ' . $orderClassName)); 

    } else {
      // Autre role
      return $this->findColorOrderDisplay($this->wipId);
    }    
  }
    
  /**
   * Récupération des couleurs des commandes  
   *
   * @return string - ClassName
   */
  function findColorOrderDisplay(int $wipId = null): string {

    if($wipId) {
      $this->wipId = $wipId;
    }

    switch($this->wipId) {
      // Preflight
      case in_array($this->wipId, StaticData::PREFLIGHT_ID):
        return StaticData::CLASS_NAME_ORDERS_COLORS['PREFLIGHT_CLASS_NAME'];
      break;

      // Progress
      case in_array($this->wipId, StaticData::ON_PROGRESS_ID):
        return StaticData::CLASS_NAME_ORDERS_COLORS['PROGRESS_CLASS_NAME'];
      break;

      // Ready
      case in_array($this->wipId, StaticData::READY_ID): 
        return StaticData::CLASS_NAME_ORDERS_COLORS['READY_CLASS_NAME'];
      break;

      // Delivered
      case in_array($this->wipId, StaticData::DELIVERED_ID): 
        return StaticData::CLASS_NAME_ORDERS_COLORS['DELIVERED_CLASS_NAME'];
      break;

      // Blocked
      case in_array($this->wipId, StaticData::BLOCKED_ID): 
        return StaticData::CLASS_NAME_ORDERS_COLORS['BLOCKED_CLASS_NAME'];
      break;
    }
  }

  /**
   * Vérification si ajout de BLINKING_CLASS_NAME pour faire clignoter la commande
   *
   * @param array $factoryWipIdArray
   * @return string - className
   */
  function isBlinking(array $factoryWipIdArray): string {
    
    // Preflight id de l'usine
    $preflightId = min($factoryWipIdArray);

    // Si wipid === StatusDelivered
    if(($preflightId - 1) === $this->wipId && in_array(($preflightId - 1), StaticData::DELIVERED_ID)) {
      return StaticData::CLASS_NAME_ORDERS_COLORS['BLINKING_CLASS_NAME']; 
    }

    return "";
  }

}