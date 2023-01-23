<?php
require_once('/home/mdwfrkglvc/www/wp-config.php');
require_once ('/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/model/User.php');
/**
 * Gestion identité de la personne connecté
 */
class UserHelper {

  /**
   * Utilisateur connecté
   *
   * @var User
   */
  protected User $user;

  function __construct()
  { 
    $this->setUserInformation();
    
  }

  /**
   * Renvoie les informations d'un utilisateur
   *
   * @return User
   */
  function getUser(): User {
    return $this->user;
  }

  /**
   * Récupération des informations d'un utilisateur
   *
   * @return void
   */
  private function setUserInformation(): void {
    // Récupération UserWordPress    
    $currentUser = wp_get_current_user();    
    $firstName = $currentUser->first_name;
    $lastName = $currentUser->last_name;    
    $userId = $currentUser->ID;
    $roles = get_userdata($userId)->roles;

    // Utilisateur
    $user = new User($userId, $firstName, $lastName, $roles);

    $this->user = $user;
    
  }
}