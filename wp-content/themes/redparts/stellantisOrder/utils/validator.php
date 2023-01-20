<?php
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/model/User.php';

/**
   * Vérifie l'existence d'un Role utilisateur
   *
   * @param string $role
   * @return void
   */
  function isUserRoleFind(User $user, string $role) {
    // Vérification des droits      
  foreach($user->getRoles() as $userRole) {
    if( strtolower($userRole) === strtolower($role)) {
      return true;
    }
  }    
  return false;
}

/**
 * Valide une date
 *
 * @param string $date - date a vérifier
 * @return void
 */
function isDateValid(string $date) {
  $year =(int)date("Y",strtotime($date));
  $month =(int)date("m",strtotime($date));
  $day = (int)date("d",strtotime($date));

  return checkdate($month, $day, $year);
}