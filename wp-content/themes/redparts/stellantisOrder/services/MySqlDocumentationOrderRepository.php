<?php
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/model/pdfModel/DocumentationOrderModel.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/interfaces/DocumentationOrderInterface.php';
require_once('/home/mdwfrkglvc/www/wp-config.php');

/**
 * Repository MySqlPartNumberToPDF 
 */
class MySqlDocumentationOrderRepository implements DocumentationOrderInterface {
  

  /**
   * Sauvegarde 
   *
   * @param DocumentationOrderModel $OrderPartNumber
   * @param int $orderId
   * @return int - id documentationOrder
   */
  function save(DocumentationOrderModel $documentationOrder, int $orderId): int {
    global $wpdb;
    $wpdb->insert('documentationsOrders', array(
      'partNumberToPDFId' => $documentationOrder->getPartNumberToPDFId(),
      'orderId' => $orderId,
      'docRef' => $documentationOrder->getDocumentRef(),
    ));
    return $wpdb->insert_id;
  }

  /**
   * Recherche de tous les liens d'un PartNumber ver une documentation
   *
   * @param integer $orderId
   * @param integer $partNumberToPDFId
   * @param string $docRef
   * @return array
   */
  function findByOrderId(int $orderId, int $partNumberToPDFId, string $docRef): array {
    global $wpdb;
    $query = "SELECT * FROM documentationsOrders 
              JOIN partNumberToPDF ON documentationsOrders.partNumberToPDFId = partNumberToPDF.id
              WHERE partNumberToPDFId = '" .$partNumberToPDFId ."' AND orderId ='".$orderId."' AND docRef = '".$docRef."' LIMIT 1";
    $findPDFLink = $wpdb->get_results($query, ARRAY_A);

    return $findPDFLink;
  }

}