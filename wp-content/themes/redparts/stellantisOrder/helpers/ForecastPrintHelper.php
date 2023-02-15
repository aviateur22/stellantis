<?php
require_once ('/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/services/MySqlForecastRepository.php');
require_once ('/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/model/RepositoriesModel.php');
require_once ('/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/interfaces/ForecastRepositoryInterface.php');
require_once ('/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/utils/StaticData.php');

/**
 * Helper prévision impression
 */
class ForecastPrintHelper {
 
  /**
   * Forecast respository
   *
   * @var ForecastRepositoryInterface
   */
  protected ForecastRepositoryInterface $forecastRepository;
  
  
  function __construct(RepositoriesModel $repositories) {   
    $this->forecastRepository = $repositories->getForecastRepository();
  }
  
  /**
   * Récupération des Quantité prévu
   * 
   * @param string $orderDeliveredDate - Date de livraison de la commande
   * @param string $partNumber - PartNumber de la commande
   * @param int orderQuantity- Quantité dela commande
   * 
   * @return int
   */
  function getForecastOrdersQuantity(string $orderDeliveredDate, string $partNumber): int {

    // Nombre de semaine de prévision
    $forecastWeek = '+' . StaticData::PRINT_FORECAST_WEEK . ' week';
    
    // Jour de début
    $dayStart = date('Y-m-d', strtotime( '+1 day', strtotime($orderDeliveredDate)));

    // Jour de fin
    $dayEnd = date('Y-m-d', strtotime( $forecastWeek, strtotime($dayStart)));   

    // Récupérations des forecastOrders
    $forecastOrders = $this->forecastRepository->findForecastByWeekInterval($partNumber, $dayStart, $dayEnd);    

    // $forecastOrders = $this->forecastRepository->findForecastByWeekInterval('23A1CCEDEXX57D3', $dayStart, $dayEnd);

    // Si pas de forecatOrder
    if(count($forecastOrders) === 0) {
      return 0;
    }

    // Calcul des quantitté de prévues
    $orderQuantityForecast = $this->calculForecastOrderQuantity($forecastOrders);    
    return $orderQuantityForecast;
  }

  /**
   * Calcul des prévisions de quantité
   *
   * @param array $forcastOrders
   * @return integer
   */
  private function calculForecastOrderQuantity(array $forcastOrders): int {

    // Derniere date des commandes court Terme
    $lastShortTermDeliveredDate = date('1970-01-01');

    // Quantité de prévu 
    $forecastQuantity = 0;

    // Traitement des commandes courtes terme
    foreach($forcastOrders as $forecastOrder) {
      
      // Commande Court terme
      if($forecastOrder->shortTerm === '1') {
        $forecastQuantity += (int)$forecastOrder->quantity;

        // Récupération de la nouvelle deliveredDate
        if($forecastOrder->deliveredDate > $lastShortTermDeliveredDate) {
          $lastShortTermDeliveredDate = $forecastOrder->deliveredDate;
        }
      }
    }

    // Traitement des commandes long terme
    foreach($forcastOrders as $forecastOrder) {
      // Commande Long terme
      if($forecastOrder->shortTerm === '0') {

        // Récupération de la nouvelle deliveredDate
        if($forecastOrder->deliveredDate > $lastShortTermDeliveredDate) {         
          //$lastShortTermDeliveredDate = $forecastOrder->deliveredDate;
          $forecastQuantity += (int)$forecastOrder->quantity;
        }
      }
    }
   
    return $forecastQuantity;
  }

}