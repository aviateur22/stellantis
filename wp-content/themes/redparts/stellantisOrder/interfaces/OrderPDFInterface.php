<?php
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/model/pdfModel/OrderPdfModel.php';
/**
 * Interface DocumentationOrder
 */
interface OrderPDFInterface {
  /**
   * Sauvegarde PartNumberPDF
   *
   * @param OrderPdfModel $orderPdfModel
   * @param int $orderId
   * @param int $documentationOrderId
   * @return void
   * @return void
   */
  function save(OrderPdfModel $orderPdfModel, int $orderId, int $documentationOrderId);

  /**
   * Recherche de tous les liens PDF en fonction d'un partNumberToPDFId
   *
   * @param integer $documentationOrderId
   * @return array
   */
  function findByDocumentationOrderId(int $documentationOrderId): array;
}