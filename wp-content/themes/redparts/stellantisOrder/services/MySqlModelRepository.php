<?php
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/interfaces/ModelRepositoryInterface.php';
require_once('/home/mdwfrkglvc/www/wp-config.php');

/**
 * Gestion requete SQL Model
 */
class MySqlModelRepository implements ModelRepositoryInterface {
  
  /**
   * Renvoie du modèle
   *
   * @param string $codeName
   * @return array
   */
  function findOneByCode(string $codeName): array {
    global $wpdb;
        
    $findModel = $wpdb->get_results($wpdb->prepare(
      "SELECT * FROM modelCodes WHERE LOWER(code) = %s",
      $codeName
    ), ARRAY_A);

    if(count($findModel) === 0) {
      return [];
    }
    return $findModel[0];
  }
}