<?php
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/model/Order.php';

/**
 * Model Repository pour DocumentationOrder
 */
interface DocPDFRepositoryInterface {

  /**
   * Trouve toutes les DOC PDF liés a orderID
   *
   * @param Order $order
   * @return array
   */
  function findFullGuide(Order $order): array;

  /**
   * Trouve toutes les DOC PDF liés a orderID
   *
   * @param Order $order
   * @return array
   */
  function findMaintenanceBook(Order $order): array;


}