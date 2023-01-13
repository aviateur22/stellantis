<?php
require_once ('/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/interfaces/ForecastRepositoryInterface.php');

/**
 * MySQL Forecast repository
 */
class MySqlForecastRepository implements ForecastRepositoryInterface {

  /**
   * Renvoi les forecast contenu dans un inteval de semaine
   *
   * @param integer $weekStart
   * @param integer $weekEnd
   * @param string $partNumber
   * @return array
   */
  function findForecastByWeekInterval(string $partNumber, int $weekStart, int $weekEnd): array {
    global $wpdb;
    $findOrders = $wpdb->get_results($wpdb->prepare(
      "SELECT * FROM forecasts WHERE partNumber = %s AND `week` >= %d AND `week` <= %d",
      $partNumber, $weekStart, $weekEnd
      )
    );      
    return $findOrders;
  }

}
