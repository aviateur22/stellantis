<?php
/**
 * Model PartNumberPDFPrint
 */
class PartNumberPDFPrintModel {
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
   * Documentation trouvé
   *
   * @var boolean
   */
  protected bool $isDocumentationFind;
  
  function __construct(
    int $documentationOrderId,
    int $PDFPrintId,
    bool $isDocumentationFind
  ) {
    $this->documentationOrderId = $documentationOrderId;
    $this->PDFPrintId = $PDFPrintId;    
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
}