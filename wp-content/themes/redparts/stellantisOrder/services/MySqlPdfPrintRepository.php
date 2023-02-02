<?php
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/interfaces/PdfPrintRepositoryInterface.php';
require_once('/home/mdwfrkglvc/www/wp-config.php');

class MySqlPdfPrintRepository implements PdfPrintRepositoryInterface {
  
  /**
   * Récupération du lien PDF d'une documentation
   *
   * @param string $linkName
   * @param string $PDF_INT_COUV - Intereur ou Couverture
   * @return array
   */
  function findByLinkName(string $linkName, string $PDF_INT_COUV): array {
    global $wpdb;
    $query = "SELECT * FROM pdfPrints WHERE link LIKE '%" .$linkName ."%' AND LOWER(intOrCouv) = '".$PDF_INT_COUV."' ORDER BY date_maj DESC LIMIT 1";
    $findPDFLink = $wpdb->get_results($query, ARRAY_A);

    if(count($findPDFLink) === 0) {
      return [];
    }

    return $findPDFLink[0];
  }
}