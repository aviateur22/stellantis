<?php
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/model/Order.php';

/**
 * Interface pour le repository
 */
interface OrderRepositoryInterface {

  /**
   * Sauvegarde de plusieurs commande
   *
   * @param array $orders - Liste des commandes à sauvgarder
   * @return void
   */
  function saveList(array $orders): void;

  /**
   * Sauvegarde de 1 commande
   *
   * @param Order $order
   * @return array
   */
  function save(Order $order): int;

  /**
   * Recherche d'une commande a partir id de la commande
   *
   * @param string $orderId
   * @return array
   */
  function findOne(string $orderId): array;

  /**
   * Trouve les commandes dupliquées
   *
   * @param array $orders - Liste des commandes a vérifier
   * @return array - Commandes dupliquées
   */
  function findDuplicatedOrder(array $orders): array;

  /**
   * Trouve les commandes dupliquées pour 1 partNumber
   *
   * @param string $partNumber - Le partNumber de la commande a vérifier
   * @param string $deliveredDate - Date de livraison
   * @param string $orderBuyer - Usine de commande
   * @return array - Commandes dupliquées
   */
  function findOneDuplicatedOrder(string $partNumber, string $deliveredDate, string $orderBuyer): array;

  /**
   * Renvoie les commandes en erreur
   * 
   * @param string $orderId - OrderId
   *
   * @return array
   */
  function findErrorOrders(string $orderId): array;

  /**
   * Suppression d'une commande
   *
   * @param string $partNumber
   * @param string $orderId
   * @param string $deliveredDate
   * @return void
   */
  function deleteOne(string $partNumber, string $orderId, string $deliveredDate): void;

  /**
   * Récupère toutes les commandes pour un orderId
   *
   * @param string $orderId
   * @return array - Liste des commandes
   */
  function findAllByOrderId(string $orderId): array;

  /**
   * Modidfication des statuts d'une commande suivant le orderId
   *
   * @param string $orderId
   * @return void
   */
  function updateWip(string $orderId): void;

  /**
   * Mise a jour d'une commande
   *
   * @param string $orderId
   * @param string $quantity
   * @param string $deliveredDate
   * @param string $status
   * @return void
   */
  function update(string $orderId, string $quantity, string $deliveredDate, ?string $status = null): int; 

  /**
   * Suppression des ancienne commandes non traité
   * 
   * @param string $referenceDeleteDate - Date avant laquelle toutes les commandes non traitées doivent être supprimées
   *
   * @return void
   */
  function deleteUnused(string $referenceDeleteDate);

  /**
   * Recherche toutes les commandes sur un certains nombre de jour 
   *
   * @param string $dayStart
   * @param string $dayEnd
   * @return array
   */
  function findOrdersOnIntervalDay(string $daySart, string $dayEnd): array;

  /**
   * Recherche toutes les commandes filtré par date + partNumber
   *
   * @param string $daySart
   * @param string $dayEnd
   * @param string $partnumber
   * @return array
   */
  function findOrdersWithFilterPartNumber(string $dayStart, string $dayEnd, array $partNumberArray): array;

  /**
   * Recherche de la première commande qui suit un date donnée
   *
   * @param string $specifiedDay
   * @return array
   */
  function findFirstOrderAfteSpecifiedDay(string $specifiedDay): array;
}