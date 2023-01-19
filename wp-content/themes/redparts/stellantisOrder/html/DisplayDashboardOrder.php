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

    $html = "<div style='margin-right:-10%;margin-left:-10%;padding:10px;background-color:#FFF;border:solid 1px;border-radius:5px;width:120%;overflow-x: auto;white-space: nowrap; min-height: 500px'>";
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
              if(empty($quantity['order']['quantity'])) {
                $html .= "<td>";
                  $html .= "<div class='inprogress'>";
                    $html .= '-';
                  $html .= "</div>";
                $html .= "</td>";
              } else {
                $html .= "<td onclick='displayUpdateOrderElement(this);' data-order-id=".$quantity['order']['id']." >";
                  $html .= "<div class='inprogress'>";
                    $html .= $quantity['order']['quantity'];
                  $html .= "</div>";
                $html .= "</td>";
              }             
            }
          $html .= "</tr>";
        }          
      }     
    }

    // Fin HTML
    $html .= "</tbody>";
    $html .= "</table>";
    $html .= "</div>";
    // Modal information
    $html .= $this->popup();
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

  private function popup() {
    return '
          <div id="updateOrder" class="update_modal">
            <div id="loader" class="modal__loader">
              <h4 class="modal__title"> Loading in progress </h4>
              <!--Loader -->
              <div class="spinner">             
                <div class="bounce1"></div>
                <div class="bounce2"></div>
                <div class="bounce3"></div>
              </div>
             </div>          
            <form id="updateOrderForm" class="update__form" method="post">
              <h4 class="update__title">Order information</h4>
              <div class="update__content">
                <input id="id" name="id" type="hidden">
                <div class="group__control">
                  <label for="quantity">PartNumber</label>
                  <p id="displayPartNumber">XXXX</p>
                </div>
                <div class="group__control">
                  <label for="quantity">Quantity</label>
                  <input id="displayQuantity" name="quantity" id="quantity" type="number">
                </div>
                <div class="group__control">
                  <label for="deliveredDate">Delivered Date</label>
                  <input id="displayDeliveredDate" name="deliveredDate" id="deliveredDate" type="date">
                </div>
                <div class="group__control">
                  <label for="status">Order Status</label>
                  <select id="displayStatus" name="status" id="status">
                    <option value="">--Please choose an option--</option>
                    <option value="preflight">PREFLIGHT</option>
                    <option value="onprogress">ONPROGRESS</option>
                    <option value="ready">READY</option>
                    <option value="delivered">DELIVERED</option>>
                  </select>
                </div>
              </div>
              <div class="modal__button__container">
                <div>          
                  <button type="submit" class="modal__button" value> Update Order </button>
                  <button onclick="hideUpdateOrder();" type="button" class="modal__button cancel--button" value> Non </button>
                </div>
              </div>
            </form>';
  }

  private function filter() {

  }
}