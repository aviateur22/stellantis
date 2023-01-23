<?php
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/model/User.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/utils/StaticData.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/utils/validators.php';

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
    // Vérification si rôle utilisateur dans la liste des roles des usine de STELLANTIS
    $userRoleFind = isUserRoleFindInArrayOfRoles($this->user, StaticData::FACTORY_STELLANTIS_ROLES_NAMES);
    
    return $userRoleFind;
  }
}