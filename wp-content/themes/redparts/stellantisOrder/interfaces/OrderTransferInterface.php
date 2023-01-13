<?php
/**
 * Interface transfert commandes vers Maury
 */
interface OrderTransferInterface { 

  /**
   * Transfert des commandes formatée vers le client final
   *
   * @param array $orderPaths
   * @return void
   */
  function transfertOrders(array $orderPaths): void;

  /**
   * Récupération addresse d'envoie
   *
   * @param int $OrderQuantity - quantité a imprimer
   * @return string
   */
  function getFactoryRecipient(int $orderQuantity);

  /**
   * Modifie le status des Commandes
   *
   * @return void
   */
  function updateOrderStatus(): void;
}