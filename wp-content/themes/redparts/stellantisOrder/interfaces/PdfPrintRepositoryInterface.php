<?php
/**
 * Interface transfert commandes vers Maury
 */
interface PdfPrintRepositoryInterface { 

  /**
   * Récupération du lien PDF d'une documentation
   *
   * @param string $linkName
   * @return array
   */
  function findByLinkName(string $linkName): array;
}