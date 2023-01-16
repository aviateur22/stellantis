<?php
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/model/Order.php';

/**
 * Interface pour le repository
 */
interface OrderRepositoryInterface {

  /**
   * Insert plusieurs commande
   *
   * @param array $orders - Liste des commandes à sauvgarder
   * @return void
   */
  function save(array $orders): void;

  /**
   * Trouve 1 commande
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
   * @return array - Commandes dupliquées
   */
  function findOneDuplicatedOrder(string $partNumber, string $deliveredDate): array;


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
   * @param string $wipValue
   * @param string $orderId
   * @return void
   */
  function updateWip(string $wipValue, string $orderId): void;

  /**
   * Suppression des ancienne commandes
   *
   * @return void
   */
  function deleteOld(); 
}