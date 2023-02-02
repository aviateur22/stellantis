<?php
/**
 * Model pour La documentation PDF d'une commande
 */
class DocumentationOrderModel {
  
  /**
   * ID PartNumberTo
   *
   * @var integer
   */
  protected int $PartNumberToPDFId; 

  /**
   * SB ou QG documentation text
   *
   * @var string
   */
  protected string $documentationRef;

  /**
   * liste de OrderPdf
   *
   * @var array
   */
  protected array $orderPdfs;



  function __construct(
    int $PartNumberToPDFId,  
    string $documentationRef,
    array $orderPdfs
  ) {       
    $this->PartNumberToPDFId = $PartNumberToPDFId;    
    $this->documentationRef = $documentationRef;
    $this->orderPdfs = $orderPdfs;
  } 
  
  #region Getter

    /**
     * Renvoi partNumberToPDF Id
     *
     * @return integer
     */
    function getPartNumberToPDFId(): int {
      return $this->PartNumberToPDFId;
    }  

    /**
     * Documentation Ref
     *
     * @return string
     */
    function getDocumentRef(): string {
      return $this->documentationRef;
    }

    function getOrderPdfs(): array {
      return $this->orderPdfs;
    }
  #endRegion

}