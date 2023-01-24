<?php
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/interfaces/LanguageRepositoryInterface.php';

/**
 * Gestion requete SQL Model
 */
class MysqlLanguageRepository implements LanguageRepositoryInterface {
  
  function findOneByCode(string $codeName): string {
    return '';
  }
}