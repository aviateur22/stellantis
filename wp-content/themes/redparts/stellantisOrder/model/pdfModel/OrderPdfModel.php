<?php
/**
 * Model orderPdf
 */
class OrderPdfModel {
  /**
   * ID documentation en base de données
   *
   * @var integer
   */
  protected int $PDFPrintId;

  /**
   * Id partNumberToPD
   *
   * @var integer
   */
  protected int $documentationOrderId;

  /**
   * Id order
   *
   * @var integer
   */
  protected int $oderId;
 
  /**
   * Documentation trouvé
   *
   * @var boolean
   */
  protected bool $isDocumentationFind;
  
  function __construct(
    int $documentationOrderId,
    int $PDFPrintId,
    int $orderId,
    bool $isDocumentationFind
  ) {
    $this->documentationOrderId = $documentationOrderId;
    $this->PDFPrintId = $PDFPrintId;    
    $this->oderId = $orderId;
    $this->isDocumentationFind = $isDocumentationFind;
  }

  /**
   * Renvoie PartNumberToPdfId
   *
   * @return integer
   */
  function getDocumentationOrderId(): int {
    return $this->documentationOrderId;
  }

  /**
   * Renvoie PDFPrintId
   *
   * @return integer
   */
  function getPDFPrintId(): int {
    return $this->PDFPrintId;
  }

  /**
   * Documentation trouvé
   *
   * @return boolean
   */
  function getIsDocumentationFind(): bool {
    return $this->isDocumentationFind;
  }

  /**
   * OrderId
   *
   * @return integer
   */
  function getOrderId(): int {
    return $this->oderId;
  }
}