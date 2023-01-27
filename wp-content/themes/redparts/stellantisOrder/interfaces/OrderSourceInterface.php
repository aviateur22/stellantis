<?php

/**
 * Interface récupération données 
 */
interface OrderSourceInterface {

  /**
   * Vérification du fichier commande
   *
   * @return boolean
   */
  function isOrderFileValid(): bool;

  /**
   * Lecture des données
   *
   * @return void
   */
  function readOrderSourceData(): void; 

  /**
   * Renvoie la liste des commandes
   *
   * @return array
   */
  function getOrders(): array;
}