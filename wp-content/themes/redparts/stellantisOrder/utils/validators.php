<?php
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/model/User.php';

/**
 * Vérifie l'existence d'un Role utilisateur
 *
 * @param User $user - Utilisateur connecté
 * @param string $role
 * @return bool
 */
function isUserRoleFind(User $user, string $role):bool {
  // Vérification des droits      
  foreach($user->getRoles() as $userRole) {
    if( strtolower($userRole) === strtolower($role)) {
      return true;
    }
  }    
  return false;
}

/**
 * Vérification existence d'un role utilisateur dans une liste de roles
 *
 * @param User $user - Utilisateur connecté
 * @param array $roles - liste des roles
 * @return boolean
 */
function isUserRoleFindInArrayOfRoles(User $user, array $roles): bool {
  // Vérification des droits
  foreach($roles as $role) {
    foreach($user->getRoles() as $userRole) {
      if( strtolower($role) === strtolower($userRole)) {
        return true;
      }
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