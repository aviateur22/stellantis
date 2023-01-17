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
   * @param FormatedOrder $formatedOrder - Parametre sur la commande a transmettre
   * @return string
   */
  function getFactoryRecipient(FormatedOrder $formatedOrder): void;

  /**
   * Modifie le status des Commandes
   *
   * @return void
   */
  function updateOrderStatus(): void;
}