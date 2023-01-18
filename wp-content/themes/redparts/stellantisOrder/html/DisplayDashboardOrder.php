<?php

/**
 * Undocumented class
 */
class DisplayDashboardOrder {
  /**
   * Liste des commandes a afficher
   *
   * @var array
   */
  protected array $dashboardOrders;

  /**
   * Liste des jours a afficher
   *
   * @var array
   */
  protected array $intervalDays;

  function __construct(array $dashboardOrders, array $intervalDays) {
    $this->dashboardOrders = $dashboardOrders;
    $this->intervalDays = $intervalDays;
  }

  /**
   * Création html
   *
   * @return string
   */
  function createHtml(): string {
    if(!count($this->dashboardOrders) > 0) {
      return "<h3 style='color:red;text-align:center;'>No order find</h3>";
     }

    $html = "<div style='margin-right:-10%;margin-left:-10%;padding:10px;background-color:#FFF;border:solid 1px;border-radius:5px;width:120%;overflow-x: auto;white-space: nowrap;'>";
    $html .= "<table id='order_table' class='order-table'>";

    // Header
    $html .= "<thead>";
    $html .= "<tr>";
      $html .= "<th>Family</th>";      
      $html .= "<th>Country code</th>";
      $html .= "<th>Country Name</th>";
      $html .= "<th>Cover Code</th>";
      $html .= "<th>Part Number</th>";
        foreach($this->intervalDays as $day) {          
          $html .= "<th>" . $day['date'] . "</th>";
        }
    $html .= "<tr>";  
    $html .= "<thead>";
        
     // Body
     $html .= "<tbody>";

     
    

    // Parcours des dashboardOrders
    foreach($this->dashboardOrders as $order) {     
      foreach($this->dashboardOrders as $order) {
        if($order instanceof DashboardOrderModel) {
          $html .= "<tr>";
            $html .= $this->createHtmlForOrderProperty($order->getFamily());
            $html .= $this->createHtmlForOrderProperty($order->getCountryCode());
            $html .= $this->createHtmlForOrderProperty($order->getCountryName());
            $html .= $this->createHtmlForOrderProperty($order->getCoverCode());
            $html .= $this->createHtmlForOrderProperty($order->getPartNumber());
            foreach($order->getQuantitiesByDate() as $quantity) {
              $html .= "<td data-order-id=".$quantity['order']['id']." >";
                $html .= $quantity['order']['quantity'];
              $html .= "</td>";
            }
          $html .= "</tr>";
        }          
      }     
    }
    $html .= "</tbody>";
    $html .= "</table>";
    $html .= "</div>";

    return $html;
  }

  /**
   * Renvoie le contenu au format HTML pour 1 propriété de Order
   *
   * @param string $orderProperty - La propriété a mettre au format HTML
   * @return string 
   */
  function createHtmlForOrderProperty(string $orderProperty): string {
    $htmlOrder = '<td>';
    $htmlOrder .=  $orderProperty;
    $htmlOrder .= '</td>';

    return $htmlOrder;
  }

  private function filter() {

  }
}