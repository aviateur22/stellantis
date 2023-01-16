<?php
require_once ('/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/interfaces/ForecastRepositoryInterface.php');

/**
 * MySQL Forecast repository
 */
class MySqlForecastRepository implements ForecastRepositoryInterface {

  /**
   * Renvoi les forecast contenu dans un inteval de semaine
   *
   * @param string $partNumber
   * @param string $dayStart
   * @param string $dayEnd
   * @return array
   */
  function findForecastByWeekInterval(string $partNumber, string $dayStart, string $dayEnd): array {
    global $wpdb;
    $findOrders = $wpdb->get_results($wpdb->prepare(
      "SELECT * FROM forecasts WHERE partNumber = %s AND `deliveredDate` >= %s AND `deliveredDate` <= %s",
      $partNumber, $dayStart, $dayEnd
      )
    );      
    return $findOrders;
  }

} 
