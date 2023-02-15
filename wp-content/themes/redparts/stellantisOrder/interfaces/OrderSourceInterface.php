<?php

/**
 * Interface récupération données 
 */
interface OrderSourceInterface {
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