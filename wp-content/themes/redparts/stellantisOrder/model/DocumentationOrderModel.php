<?php
/**
 * Model pour La documentation PDF d'une commande
 */
class DocumentationOrderModel {

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
   * Link documentation
   *
   * @var string
   */
  protected string $documentationPDFLink;

  /**
   * ID docuementation en base de données
   *
   * @var integer
   */
  protected int $documentationPDFId;

  /**
   * Id de la commande
   *
   * @var integer
   */
  protected int $orderId;

  /**
   * Documentation valid
   *
   * @var boolean
   */
  protected bool $isDocumentationFind;

  function __construct(string $documentationType, string $documentationSubType, string $documentationPDFLink, int $documentationPDFId, bool $isDocumentationFind) {
    $this->isDocumentationFind = $isDocumentationFind;
    $this->documentationPDFId = $documentationPDFId;
    $this->documentationPDFLink =  $documentationPDFLink;
    $this->documentationType = $documentationType;
    $this->documentationSubType = $documentationSubType;
  }

  /**
   * Renvoi si la documentation est trouvéd
   *
   * @return bool
   */
  function getIsDocumentationFind():bool {
    return $this->isDocumentationFind;
  }

}