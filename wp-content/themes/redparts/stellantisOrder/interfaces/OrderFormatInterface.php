<?php
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/model/Order.php';

/**
 * Interface Format des commandes a transferer
 */
interface OrderFormatInterface {

  /**
   * Transforme la commande au format requis par le client
   *
   * @return array - Array de Object FormatedOrder
   */
  function createFormatedOrders(): array;  
}