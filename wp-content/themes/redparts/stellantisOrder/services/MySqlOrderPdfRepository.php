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
   * @return void
   */
  function save(OrderPdfModel $orderPdfModel) {
    global $wpdb;
    $addorderPdf = $wpdb->insert('orderPdfs', array(
      'documentationOrderId' => $orderPdfModel->getDocumentationOrderId(),
      'PDFPrintId' => $orderPdfModel->getDocumentationOrderId(),
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