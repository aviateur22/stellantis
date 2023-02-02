<?php
/**
 * Interface transfert commandes vers Maury
 */
interface PdfPrintRepositoryInterface { 

  /**
   * Récupération du lien PDF d'une documentation
   *
   * @param string $linkName
   * @param string $PDF_INT_COUV - Intereur ou Couverture
   * @return array
   */
  function findByLinkName(string $linkName, string $PDF_INT_COUV): array;
}