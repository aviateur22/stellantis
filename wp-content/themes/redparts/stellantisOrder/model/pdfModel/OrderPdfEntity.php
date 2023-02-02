<?php
/**
 * Entity  OrderPdf
 */
class OrderPdfEntity {

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
  protected int $documentationOrderId;

  /**
   * Id OrderId
   *
   * @var integer
   */
  protected int $orderId;

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
   * Documentation Trouvée
   *
   * @var boolean
   */
  protected bool $isDocumentationFind;

  function __construct(
    int $id,
    int $documentationOrderId,
    int $PDFPrintId,
    int $orderId,
    string $documentationPDFLink, 
    bool $isDocumentationFind
  ) {
    $this->id = $id;
    $this->documentationOrderId = $documentationOrderId;
    $this->orderId = $orderId;
    $this->PDFPrintId = $PDFPrintId;
    $this->isDocumentationFind = $isDocumentationFind;    
    $this->documentationPDFLink = $documentationPDFLink;
  }

  #region Getter
    function getId(): int {
      return $this->id;
    }

    function getDocumentationOrderId(): int {
      return $this->documentationOrderId;
    }

    function getPDFPrintId(): int {
      return $this->PDFPrintId;
    }

    function getIsDocumentationFind(): bool {
      return $this->isDocumentationFind;
    }

    function getDocumentationPDFLink(): string {
      return $this->documentationPDFLink;
    }
  #endRegion
}