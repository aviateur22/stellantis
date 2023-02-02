<?php
/**
 * Entity  PartNumberPDFPrint
 */
class PartNumberPDFPrintEntity {

  /**
   * Id
   *
   * @var integer
   */
  protected int $id;

  /**
   * ID documentation en base de données
   *
   * @var integer
   */
  protected int $PDFPrintId;

  /**
   * Id partNumberToPdf
   *
   * @var integer
   */
  protected int $partNumberToPDFId;

  /**
   * Link documentation
   *
   * @var string
   */
  protected string $documentationPDFLink;
  

  /**
   * Documentation PDF couverture
   *
   * @var bool
   */
  protected bool $isDocumentationCouv;


  /**
   * Documentation valid
   *
   * @var boolean
   */
  protected bool $isDocumentationFind;

  function __construct(
    int $id,
    int $partNumberToPDFId,
    int $PDFPrintId,
    string $documentationPDFLink, 
    bool $isDocumentationFind
  ) {
    $this->id = $id;
    $this->partNumberToPDFId = $partNumberToPDFId;
    $this->PDFPrintId = $PDFPrintId;
    $this->isDocumentationFind = $isDocumentationFind;    
    $this->documentationPDFLink = $documentationPDFLink;
    
  }
}