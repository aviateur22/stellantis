<?php
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/model/pdfModel/OrderPdfModel.php';
/**
 * Interface DocumentationOrder
 */
interface OrderPDFInterface {
  /**
   * Sauvegarde PartNumberPDF
   *
   * @param PartNumberPDFPrintModel $partNumberPDF
   * @return void
   */
  function save(OrderPdfModel $orderPdfModel);

  /**
   * Recherche de tous les liens PDF en fonction d'un partNumberToPDFId
   *
   * @param integer $documentationOrderId
   * @return array
   */
  function findByDocumentationOrderId(int $documentationOrderId): array;
}