<?php
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/interfaces/ModelRepositoryInterface.php';

/**
 * Gestion requete SQL Model
 */
class MySqlModelRepository implements ModelRepositoryInterface {
  
  function findOneByCode(string $codeName): string {
    return '';
  }
}