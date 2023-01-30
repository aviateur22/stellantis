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
 * @return bool
 */
function isDateValid(string $date): bool {
  // Vérifie les date 1-jan-1970
  if(date('Y-m-d', strtotime($date)) === date('Y-m-d', strtotime('1970-01-01'))) {
    return false;
  }

  $year =(int)date("Y",strtotime($date));
  $month =(int)date("m",strtotime($date));
  $day = (int)date("d",strtotime($date));

  return checkdate($month, $day, $year);
}

/**
 * Récupération des couleurs des commandes  
 *
 * @param string $wipId
 * @return string - ClassName
 */
function findColorOrderDisplay(string $wipId): string {
  switch($wipId) {
    // Before Preflight
    case in_array($wipId, StaticData::BEFORE_PREFLIGHT_ID):
      return StaticData::CLASS_NAME_ORDERS_COLORS['BEFORE_PREFLIGHT_CLASS_NAME'];
    break;

    // Preflight
    case in_array($wipId, StaticData::PREFLIGHT_ID):
      return StaticData::CLASS_NAME_ORDERS_COLORS['PREFLIGHT_CLASS_NAME'];
    break;

    // Progress
    case in_array($wipId, StaticData::ON_PROGRESS_ID):
      return StaticData::CLASS_NAME_ORDERS_COLORS['PROGRESS_CLASS_NAME'];
    break;

    // Ready
    case in_array($wipId, StaticData::READY_ID): 
      return StaticData::CLASS_NAME_ORDERS_COLORS['READY_CLASS_NAME'];
    break;

    // Delivered
    case in_array($wipId, StaticData::DELIVERED_ID): 
      return StaticData::CLASS_NAME_ORDERS_COLORS['DELIVERED_CLASS_NAME'];
    break;

    // Blocked
    case in_array($wipId, StaticData::BLOCKED_ID): 
      return StaticData::CLASS_NAME_ORDERS_COLORS['BLOCKED_CLASS_NAME'];
    break;
  }
}