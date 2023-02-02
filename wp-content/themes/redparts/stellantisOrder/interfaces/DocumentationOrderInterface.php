<?php
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/model/pdfModel/DocumentationOrderModel.php';
/**
 * Interface DocumentationOrder
 */
interface DocumentationOrderInterface {

  /**
   * Sauvegarde 
   *
   * @param OrderPartNumberToPDFModel $OrderPartNumber
   * @return void
   */
  function save(DocumentationOrderModel $documentationOrder);

  /**
   * Recherche de tous les liens d'un PartNumber ver une documentation
   *
   * @param integer $orderId
   * @param integer $partNumberToPDFId
   * @param string $docRef
   * @return array
   */
  function findByOrderId(int $orderId, int $partNumberToPDFId, string $docRef): array;
}