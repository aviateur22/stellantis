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
   * Documentation trouvé
   *
   * @var boolean
   */
  protected bool $isDocumentationFind;
  
  function __construct(   
    int $PDFPrintId,   
    bool $isDocumentationFind
  ) {    
    $this->PDFPrintId = $PDFPrintId;  
    $this->isDocumentationFind = $isDocumentationFind;
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