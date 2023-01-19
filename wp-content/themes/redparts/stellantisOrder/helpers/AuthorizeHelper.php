<?php
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/model/User.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/utils/StaticData.php';

class AuthorizeHelper {

  /**
   * Utilisateur a controller
   *
   * @var User
   */
  protected User $user;

  function __construct($user)
  { 
    $this->user = $user;    
  }

  /**
   * Vérification si utilisateur habilité a déposer une commande
   * 
   * @return bool
   */
  public function isUserAuthorizeForNewOrder(): bool {
    // Roles autorisés pour faire une commande
    $authorizeOrderRoles = StaticData::FACTORY_AUTH_ORDER; 
    
    // Role de l'utilisateur connecté
    $userRoles = $this->user->getRoles();
    
    // Vérification des droits
    foreach($authorizeOrderRoles as $orderRole) {
      foreach($userRoles as $userRole) {
        if( strtolower($orderRole) === strtolower($userRole)) {
          return true;
        }
      }
    }
    return false;
  }
}