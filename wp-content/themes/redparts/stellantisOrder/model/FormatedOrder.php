<?php
/**
 * Modele fichier commande a transférer au client
 */
class FormatedOrder {
  /**
   * Path du fichier a envoyer
   *
   * @var string
   */
  protected string $filePath;

  /**
   * Quantité d'impréssion
   *
   * @var int
   */
  protected string $orderQuantity;

  /**
   * Nom du fichier
   *
   * @var string
   */
  protected string $fileName;

  function __construct(string $filePath, int $orderQuantity, string $fileName) {
    $this->filePath = $filePath;
    $this->orderQuantity = $orderQuantity;
    $this->fileName = $fileName;
  }

  #Region getter
    /**
     * Quantité de commande
     *
     * @return integer
     */
    function getOrderQuantity(): int {
      return $this->orderQuantity;
    }

    /**
     * Path du fichier
     *
     * @return string
     */
    function getOrderFilePath(): string {
      return $this->filePath;
    }

    function getFileName(): string {
      return $this->fileName;
    }
  #EndRegion
}