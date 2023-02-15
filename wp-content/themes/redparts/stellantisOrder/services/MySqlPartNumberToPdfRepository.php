<?php
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/interfaces/PartNumberToPDFInterface.php';
require_once('/home/mdwfrkglvc/www/wp-config.php');
/**
 * Repository PartNumberToPdf
 */
class MySqlPartNumberToPdfRepository implements PartNumberToPDFInterface {

  /**
   * Recherche tous les liens en fonction d'un partNumber
   *
   * @param string $partNumber
   * @return array
   */
  function findByPartNumber(string $partNumber): array {
    global $wpdb;
    $query = "SELECT * FROM PartNumberToPDF WHERE partNumber = '" .$partNumber ."'";
    $findPartNumbers = $wpdb->get_results($query, ARRAY_A);

    return $findPartNumbers;
  }
}