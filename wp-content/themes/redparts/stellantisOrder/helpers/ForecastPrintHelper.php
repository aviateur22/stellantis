<?php
require_once ('/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/services/MySqlForecastRepository.php');
require_once ('/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/interfaces/ForecastRepositoryInterface.php');

/**
 * Helper prévision impression
 */
class ForecastPrintHelper {
  /**
   * Nombre de semaine
   */
  const FORECAST_WEEK = 8;

  /**
   * Forecast respository
   *
   * @var ForecastRepositoryInterface
   */
  protected ForecastRepositoryInterface $forecastRepository;
  
  function __construct(ForecastRepositoryInterface $forecastRepository) {   
    $this->forecastRepository = $forecastRepository;
  }
  
  /**
   * Récupération des Quantité prévu
   * 
   * @param string $orderDeliveredDate - Date de livraison de la commande
   * @param string $partNumber - PartNumber de la commande
   * @param int orderQuantity- Quantité dela commande
   * 
   * @return void
   */
  function getForecastOrdersQuantity(string $orderDeliveredDate, string $partNumber, int $orderQuantity): int {
    // N° de semaine de deliveredDate
    $weekNumberStart = $this->getOrderDateWeekNumber($orderDeliveredDate);

    // N° de semaine de Fin de forecast
    $weekNumberEnd = $weekNumberStart + self::FORECAST_WEEK;

    // $forecastOrders = $this->forecastRepository->findForecastByWeekInterval($partNumber, $weekNumberStart, $weekNumberEnd);
    $forecastOrders = $this->forecastRepository->findForecastByWeekInterval('23A1CCEDEXX57D3', $weekNumberStart, $weekNumberEnd);
    //23A1CCEDEXX57D3

    var_dump($forecastOrders);

    // Si pas de forecatOrder
    if(count($forecastOrders) === 0) {
      return 0;
    }

    // Calcul des quantitté de prévues
    $orderQuantityForecast = $this->calculForecastOrderQuantity($forecastOrders);
    var_dump($orderQuantityForecast);
    var_dump($orderQuantityForecast - $orderQuantity);
    return $orderQuantityForecast - $orderQuantity;
  }

  /**
   * Récupération du N° de la semaine
   *
   * @param string $orderDate
   * @return integer
   */
  private function getOrderDateWeekNumber(string $orderDate): int {
    $weekNumber = date('W', strtotime($orderDate));
    return (int)$weekNumber;
  }

  /**
   * Calcul des prévisions de quantité
   *
   * @param array $forcastOrders
   * @return integer
   */
  private function calculForecastOrderQuantity(array $forcastOrders): int {

    // Liste des commandes long terme
    $longTermOrders = [];

    // Quantité de prévu 
    $forecastQuantity = 0;

    // Traitement des commandes courtes terme
    foreach($forcastOrders as $forecastOrder) {
      // Commande Court terme
      if($forecastOrder->shortem === 0) {
        $forecastQuantity += (int)$forecastOrder->quantity;
      } else {
        // Ajout d'une commande long terme
        $longTermOrders [] = $forecastOrder;
      }
    }

    // Traitement Commande longterme
    foreach($longTermOrders as $longTermOrder) {
      $forecastQuantity += (int)$longTermOrder->quantity;
    }

    return $forecastQuantity;
  }

}