<?php
require_once ('/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/model/pdfModel/OrderPdfEntity.php');
/**
 * Entity OrderPartNumberToPDF
 */
class DocumentationOrderEntity {

  /**
   * id
   *
   * @var integer
   */
  protected int $id;
 
  /**
   * Id partNumberToPDF Id
   *
   * @var integer
   */
  protected int $partNumberToPDFId;

  /**
   * Id de la commande
   *
   * @var integer
   */
  protected int $orderId;

  /**
   * Type de documentation (FullGuide, MaintenanceBook)
   *
   * @var string
   */
  protected string $documentationType;

  /**
   * Sous-type de documentation (CCE, CCEE, CCI..)
   *
   * @var string
   */
  protected string $documentationSubType; 

  /**
   * wallet branded pour l'impression de la doc
   *
   * @var bool
   */
  protected bool $isWalletBranded;

  /**
   * paperWallet pour l'impression commande
   *
   * @var bool
   */
  protected bool $isPaperWallet;

  /**
   * list of Type PartNumberPDFPrintEntity
   *
   * @var array
   */
  protected array $partNumberPDFPrintEntity;

  function __construct(
    int $id,
    int $orderId,
    int $partNumberToPDFId,    
    string $documentationType, 
    string $documentationSubType,     
    bool $isWalletBranded,
    bool $isPaperWallet
  ) {
    $this->id = $id;
    $this->orderId = $orderId;
    $this->partNumberToPDFId = $partNumberToPDFId;   
    $this->isWalletBranded = $isWalletBranded;
    $this->isPaperWallet = $isPaperWallet;
    $this->documentationType = $documentationType;
    $this->documentationSubType = $documentationSubType;
  }

  #Region getter

    function getId(): int {
      return $this->id;
    }

    function getOrderId(): int {
      return $this->orderId;
    }

    function getPartNumberToPdfId(): int {
      return $this->partNumberToPDFId;
    }

    function getIsWalletBranded(): bool {
      return $this->isWalletBranded;
    }

    function getIsPaperWalley(): bool {
      return $this->isPaperWallet;
    }

    function getDocumentationType(): string {
      return $this->documentationType;
    }

    function getDocumentaionSubType(): string {
      return $this->documentationSubType;
    }
  #endRegion
}