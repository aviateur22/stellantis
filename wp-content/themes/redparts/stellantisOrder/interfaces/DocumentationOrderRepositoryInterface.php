<?php

/**
 * Model Repository pour DocumentationOrder
 */
interface DocumentationOrderRepositoryInterface {

  /**
   * Sauvegarde des liensPDF et commandes
   *
   * @param array $documentationOrders - Liste des liens PDF-OrderId
   * @return void
   */
  function save(array $documentationOrders): void;

  /**
   * Trouve toutes les DOC PDF liés a orderID
   *
   * @param integer $orderId
   * @return array
   */
  function findAllByOrderId(int $orderId): array;
}