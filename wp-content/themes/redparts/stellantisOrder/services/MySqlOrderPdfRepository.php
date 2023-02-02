<?php
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/model/pdfModel/OrderPdfModel.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/interfaces/OrderPDFInterface.php';
require_once('/home/mdwfrkglvc/www/wp-config.php');

/**
 * Repository OrderPdfs
 */
class MySqlOrderPdfRepository implements OrderPDFInterface {

  /**
   * Sauvegarde PartNumberPDF
   *
   * @param OrderPdfModel $orderPdfModel
   * @param int $orderId
   * @param int $documentationOrderId
   * @return void
   */
  function save(OrderPdfModel $orderPdfModel, int $orderId, int $documentationOrderId) {
    global $wpdb;
    $addorderPdf = $wpdb->insert('orderPdfs', array(
      'documentationOrderId' => $documentationOrderId,
      'PDFPrintId' => $orderId,
      'orderId' => $orderPdfModel->getPDFPrintId(),
      'isDocuementationFind' => $orderPdfModel->getIsDocumentationFind(),
    ));

    return $addorderPdf;
  }

  /**
   * Recherche de tous les liens PDF en fonction d'un partNumberToPDFId
   *
   * @param integer $documentationOrderId
   * @return array
   */
  function findByDocumentationOrderId(int $documentationOrderId): array {
    global $wpdb;
    $query = "SELECT * FROM orderPdfs 
              JOIN pdfPrints ON orderPdfs.PDFPrintId = pdfPrints.id
              WHERE documentationOrderId = '" .$documentationOrderId ."'";
    $findPDFLink = $wpdb->get_results($query, ARRAY_A);

    return $findPDFLink;
  }
}