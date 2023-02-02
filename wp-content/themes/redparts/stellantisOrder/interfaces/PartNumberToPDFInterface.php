<?php

/**
 * Interface DocumentationOrder
 */
interface PartNumberToPDFInterface {
  /**
   * Recherche tous les liens en fonction d'un partNumber
   *
   * @param string $partNumber
   * @return array
   */
  function findByPartNumber(string $partNumber): array;
}