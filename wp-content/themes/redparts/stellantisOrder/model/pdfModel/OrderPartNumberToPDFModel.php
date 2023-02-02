<?php
/**
 * Model pour La documentation PDF d'une commande
 */
class OrderPartNumberToPDFModel {
  
  /**
   * ID PartNumberTo
   *
   * @var integer
   */
  protected int $PartNumberToPDFId;

  /**
   * Id de la commande
   *
   * @var integer
   */
  protected int $orderId;  

  /**
   * SB ou QG documentation
   *
   * @var string
   */
  protected string $documentationRef;



  function __construct(
    int $orderId,
    int $PartNumberToPDFId,  
    string $documentationRef
  ) {   
    $this->orderId = $orderId;
    $this->PartNumberToPDFId = $PartNumberToPDFId;    
    $this->documentationRef = $documentationRef;
  } 

  /**
   * Renvoi Order Id
   *
   * @return integer
   */
  function getOrderId(): int  {
    return $this->orderId;
  }

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

}