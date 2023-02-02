<?php
require_once ('/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/model/pdfModel/PartNumberPDFPrintEntity.php');
/**
 * Entity OrderPartNumberToPDF
 */
class OrderPartNumberToPDFEntity {

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
}