<?php
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/interfaces/PdfPrintRepositoryInterface.php';
require_once('/home/mdwfrkglvc/www/wp-config.php');

class MySqlPdfPrintRepository implements PdfPrintRepositoryInterface {
  
  /**
   * Récupération du lien PDF d'une documentation
   *
   * @param string $linkName
   * @return array
   */
  function findByLinkName(string $linkName): array {
    global $wpdb;
    $query = "SELECT * FROM pdfPrints WHERE link LIKE '%" .$linkName ."%' ORDER BY date_maj DESC LIMIT 1";
    $findPDFLink = $wpdb->get_results($query, ARRAY_A);

    if(count($findPDFLink) === 0) {
      return [];
    }

    return $findPDFLink[0];
  }
}