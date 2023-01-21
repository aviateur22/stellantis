<?php
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/model/User.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/utils/StaticData.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/utils/validators.php';

/**
 * Undocumented class
 */
class DisplayDashboardOrder {

  /**
   * Utilisateur
   *
   * @var User
   */
  protected User $user;

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

  function __construct(array $dashboardOrders, array $intervalDays, User $user) {
    $this->dashboardOrders = $dashboardOrders;
    $this->intervalDays = $intervalDays;
    $this->user = $user;
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
        if($order instanceof DashboardOrderModel) {
          $html .= "<tr>";
            $html .= $this->createHtmlForOrderProperty($order->getFamily());
            $html .= $this->createHtmlForOrderProperty($order->getCountryCode());
            $html .= $this->createHtmlForOrderProperty($order->getCountryName());
            $html .= $this->createHtmlForOrderProperty($order->getCoverCode());
            $html .= $this->createHtmlForOrderProperty($order->getPartNumber());
            foreach($order->getQuantitiesByDate() as $quantity) {
              if(empty($quantity['order']['quantity'])) {
                $html .= "<td class='td--empty'>";
                  $html .= "<div class='inprogress'>";
                    $html .= '-';
                  $html .= "</div>";
                $html .= "</td>";
              } else {
                $html .= "<td onclick='displayUpdateOrderElement(this);' data-order-id=".$quantity['order']['id']." >";
                  $html .= "<div class='td--border ".$this->getCellClass((int)$quantity['order']['wipId'])."' class='inprogress'>";
                    $html .= $quantity['order']['quantity'];
                  $html .= "</div>";
                $html .= "</td>";
              }             
            }
          $html .= "</tr>";
        }          
      }     
    

    // Fin HTML
    $html .= "</tbody>";
    $html .= "</table>";
    $html .= "</div>";

    // Modal information
    $html .= $this->popup();

    // Flash message
    $html .= $this->flashMessage();

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

  /**
   * Message utilisateur
   *
   * @return void
   */
  private function flashMessage() {
    return '
      <div id="flashMessage" class="flash__container">
        <div class="flash  success--message">
          <p id="flashMessageText" class="flash__text">Mon Text<p>
        </div>
      </div>';
  }

  /**
   * Popup modification commandes
   *
   * @return void
   */
  private function popup() {
    return'
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
            <input min="1" id="displayQuantity" name="quantity" id="quantity" type="number">
          </div>
          <div class="group__control">
            <label for="deliveredDate">Delivered Date</label>
            <input id="displayDeliveredDate" name="deliveredDate" id="deliveredDate" type="date">
          </div>
          '.$this->setPopupSelectectOption().'                                
        </div>
        <div class="modal__button__container">
          <div>          
            <button type="submit" class="modal__button" value> Update Order </button>
            <button onclick="hideUpdateOrder();" type="button" class="modal__button cancel--button" value> Non </button>
          </div>
        </div>
      </form>';
  }

  /**
   * Undocumented function
   *
   * @return void
   */
  private function setPopupSelectectOption() {
    // Utilisateur avec Role de Millau
    if(isUserRoleFind($this->user, StaticData::MILLAU_FACTORY_ROLE_NAME)) {
      return '
      <div class="group__control">
        <label for="status">Order Status</label>
        <select id="displayStatus" name="status" id="status" required>
            <option value="">--Please choose an option--</option>
            <option value='.StaticData::ORDER_STATUS['PREFLIGHT_MI'].'>'.StaticData::STATUS_DISPLAY_NAME_ROLE_ST_MI_MA['PREFLIGHT'].'</option>
            <option value='.StaticData::ORDER_STATUS['ON_PROGRESS_MI'].'>'.StaticData::STATUS_DISPLAY_NAME_ROLE_ST_MI_MA['ON_PROGRESS'].'</option>
            <option value='.StaticData::ORDER_STATUS['READY_MI'].'>'.StaticData::STATUS_DISPLAY_NAME_ROLE_ST_MI_MA['READY'].'</option>
            <option value='.StaticData::ORDER_STATUS['DELIVERED_MI'].'>'.StaticData::STATUS_DISPLAY_NAME_ROLE_ST_MI_MA['DELIVERED'].'</option>
            <option value='.StaticData::ORDER_STATUS['BLOCKED_MI'].'>'.StaticData::STATUS_DISPLAY_NAME_ROLE_ST_MI_MA['BLOCKED'].'</option>
        </select>
      </div>';

    } elseif(isUserRoleFind($this->user, StaticData::MANCHECOURT_FACTORY_ROLE_NAME)) {
      // Utilisateur avec Role de Manchecourt
      return '
      <div class="group__control">
        <label for="status">Order Status</label>
        <select id="displayStatus" name="status" id="status" required>
            <option value="">--Please choose an option--</option>
            <option value='.StaticData::ORDER_STATUS['PREFLIGHT_MA'].'>'.StaticData::STATUS_DISPLAY_NAME_ROLE_ST_MI_MA['PREFLIGHT'].'</option>
            <option value='.StaticData::ORDER_STATUS['ON_PROGRESS_MA'].'>'.StaticData::STATUS_DISPLAY_NAME_ROLE_ST_MI_MA['ON_PROGRESS'].'</option>
            <option value='.StaticData::ORDER_STATUS['READY_MA'].'>'.StaticData::STATUS_DISPLAY_NAME_ROLE_ST_MI_MA['READY'].'</option>
            <option value='.StaticData::ORDER_STATUS['DELIVERED_MA'].'>'.StaticData::STATUS_DISPLAY_NAME_ROLE_ST_MI_MA['DELIVERED'].'</option>
            <option value='.StaticData::ORDER_STATUS['BLOCKED_MA'].'>'.StaticData::STATUS_DISPLAY_NAME_ROLE_ST_MI_MA['BLOCKED'].'</option>
        </select>
      </div>';

    } elseif(isUserRoleFind($this->user, StaticData::STELLANTIS_ROLE_NAME)) {
      // Utilisateur avec role de Stellantis
      return '
      <div class="group__control">
        <label for="status">Order Status</label>
        <select id="displayStatus" name="status" id="status" disabled>
            <option value="">--Please choose an option--</option>
            <option value='.StaticData::ORDER_STATUS['PREFLIGHT_ST'].'>'.StaticData::STATUS_DISPLAY_NAME_ROLE_ST_MI_MA['PREFLIGHT'].'</option>
            <option value='.StaticData::ORDER_STATUS['ON_PROGRESS_ST'].'>'.StaticData::STATUS_DISPLAY_NAME_ROLE_ST_MI_MA['ON_PROGRESS'].'</option>
            <option value='.StaticData::ORDER_STATUS['READY_ST'].'>'.StaticData::STATUS_DISPLAY_NAME_ROLE_ST_MI_MA['READY'].'</option>
            <option value='.StaticData::ORDER_STATUS['DELIVERED_ST'].'>'.StaticData::STATUS_DISPLAY_NAME_ROLE_ST_MI_MA['DELIVERED'].'</option>
            <option value='.StaticData::ORDER_STATUS['BLOCKED_ST'].'>'.StaticData::STATUS_DISPLAY_NAME_ROLE_ST_MI_MA['BLOCKED'].'</option>
        </select>
      </div>';
    } elseif(isUserRoleFindInArrayOfRoles($this->user, StaticData::FACTORY_STELLANTIS_ROLES_NAMES)) {
      // Utilisateur avec roles des usines de Stellantis - Rennes-Poissy-Sochaux....
      return '
      <div class="group__control">
        <label for="status">Order Status</label>
        <select id="displayStatus" name="status" id="status" required disabled>
            <option value="">--Please choose an option--</option>
            <option value='.StaticData::ORDER_STATUS['PREFLIGHT_MI'].'>'.StaticData::STATUS_DISPLAY_NAME_ROLE_OTHER['PREFLIGHT_MI'].'</option>
            <option value='.StaticData::ORDER_STATUS['ON_PROGRESS_MI'].'>'.StaticData::STATUS_DISPLAY_NAME_ROLE_OTHER['ON_PROGRESS_MI'].'</option>
            <option value='.StaticData::ORDER_STATUS['READY_MI'].'>'.StaticData::STATUS_DISPLAY_NAME_ROLE_OTHER['READY_MI'].'</option>
            <option value='.StaticData::ORDER_STATUS['DELIVERED_MI'].'>'.StaticData::STATUS_DISPLAY_NAME_ROLE_OTHER['DELIVERED_MI'].'</option>
            <option value='.StaticData::ORDER_STATUS['BLOCKED_MI'].'>'.StaticData::STATUS_DISPLAY_NAME_ROLE_OTHER['BLOCKED_MI'].'</option>
            <option value='.StaticData::ORDER_STATUS['PREFLIGHT_MA'].'>'.StaticData::STATUS_DISPLAY_NAME_ROLE_OTHER['PREFLIGHT_MA'].'</option>
            <option value='.StaticData::ORDER_STATUS['ON_PROGRESS_MA'].'>'.StaticData::STATUS_DISPLAY_NAME_ROLE_OTHER['ON_PROGRESS_MA'].'</option>
            <option value='.StaticData::ORDER_STATUS['READY_MA'].'>'.StaticData::STATUS_DISPLAY_NAME_ROLE_OTHER['READY_MA'].'</option>
            <option value='.StaticData::ORDER_STATUS['DELIVERED_MA'].'>'.StaticData::STATUS_DISPLAY_NAME_ROLE_OTHER['DELIVERED_MA'].'</option>
            <option value='.StaticData::ORDER_STATUS['BLOCKED_MA'].'>'.StaticData::STATUS_DISPLAY_NAME_ROLE_OTHER['BLOCKED_MA'].'</option>
            <option value='.StaticData::ORDER_STATUS['PREFLIGHT_ST'].'>'.StaticData::STATUS_DISPLAY_NAME_ROLE_OTHER['PREFLIGHT_ST'].'</option>
            <option value='.StaticData::ORDER_STATUS['ON_PROGRESS_ST'].'>'.StaticData::STATUS_DISPLAY_NAME_ROLE_OTHER['ON_PROGRESS_ST'].'</option>
            <option value='.StaticData::ORDER_STATUS['READY_ST'].'>'.StaticData::STATUS_DISPLAY_NAME_ROLE_OTHER['READY_ST'].'</option>
            <option value='.StaticData::ORDER_STATUS['DELIVERED_ST'].'>'.StaticData::STATUS_DISPLAY_NAME_ROLE_OTHER['DELIVERED_ST'].'</option>
            <option value='.StaticData::ORDER_STATUS['BLOCKED_ST'].'>'.StaticData::STATUS_DISPLAY_NAME_ROLE_OTHER['BLOCKED_ST'].'</option>         
        </select>
      </div>';

    } else {
      return'
      <div class="group__control">
        <label for="status">Order Status</label>
        <select id="displayStatus" name="status" id="status" required>
            <option value="">--Please choose an option--</option>
            <option value='.StaticData::ORDER_STATUS['PREFLIGHT_MI'].'>'.StaticData::STATUS_DISPLAY_NAME_ROLE_OTHER['PREFLIGHT_MI'].'</option>
            <option value='.StaticData::ORDER_STATUS['ON_PROGRESS_MI'].'>'.StaticData::STATUS_DISPLAY_NAME_ROLE_OTHER['ON_PROGRESS_MI'].'</option>
            <option value='.StaticData::ORDER_STATUS['READY_MI'].'>'.StaticData::STATUS_DISPLAY_NAME_ROLE_OTHER['READY_MI'].'</option>
            <option value='.StaticData::ORDER_STATUS['DELIVERED_MI'].'>'.StaticData::STATUS_DISPLAY_NAME_ROLE_OTHER['DELIVERED_MI'].'</option>
            <option value='.StaticData::ORDER_STATUS['BLOCKED_MI'].'>'.StaticData::STATUS_DISPLAY_NAME_ROLE_OTHER['BLOCKED_MI'].'</option>
            <option value='.StaticData::ORDER_STATUS['PREFLIGHT_MA'].'>'.StaticData::STATUS_DISPLAY_NAME_ROLE_OTHER['PREFLIGHT_MA'].'</option>
            <option value='.StaticData::ORDER_STATUS['ON_PROGRESS_MA'].'>'.StaticData::STATUS_DISPLAY_NAME_ROLE_OTHER['ON_PROGRESS_MA'].'</option>
            <option value='.StaticData::ORDER_STATUS['READY_MA'].'>'.StaticData::STATUS_DISPLAY_NAME_ROLE_OTHER['READY_MA'].'</option>
            <option value='.StaticData::ORDER_STATUS['DELIVERED_MA'].'>'.StaticData::STATUS_DISPLAY_NAME_ROLE_OTHER['DELIVERED_MA'].'</option>
            <option value='.StaticData::ORDER_STATUS['BLOCKED_MA'].'>'.StaticData::STATUS_DISPLAY_NAME_ROLE_OTHER['BLOCKED_MA'].'</option>
            <option value='.StaticData::ORDER_STATUS['PREFLIGHT_ST'].'>'.StaticData::STATUS_DISPLAY_NAME_ROLE_OTHER['PREFLIGHT_ST'].'</option>
            <option value='.StaticData::ORDER_STATUS['ON_PROGRESS_ST'].'>'.StaticData::STATUS_DISPLAY_NAME_ROLE_OTHER['ON_PROGRESS_ST'].'</option>
            <option value='.StaticData::ORDER_STATUS['READY_ST'].'>'.StaticData::STATUS_DISPLAY_NAME_ROLE_OTHER['READY_ST'].'</option>
            <option value='.StaticData::ORDER_STATUS['DELIVERED_ST'].'>'.StaticData::STATUS_DISPLAY_NAME_ROLE_OTHER['DELIVERED_ST'].'</option>
            <option value='.StaticData::ORDER_STATUS['BLOCKED_ST'].'>'.StaticData::STATUS_DISPLAY_NAME_ROLE_OTHER['BLOCKED_ST'].'</option>
        </select>
      </div>';

    }
  }

  /**
   * Récupération des couleur des cellules 
   *
   * @param string $status
   * @return void
   */
  private function getCellClass(string $wipId) {    
    switch($wipId) {
      case in_array($wipId, StaticData::PREFLIGHT_ID):
        return 'status--preflight';
      break;
      case in_array($wipId, StaticData::ON_PROGRESS_ID):
        return 'status--progress';
      break;
      case in_array($wipId, StaticData::READY_ID): 
        return 'status--ready';
      break;
      case in_array($wipId, StaticData::DELIVERED_ID): 
        return 'status--delivered';
      break;
      case in_array($wipId, StaticData::BLOCKED_ID): 
        return 'status--blocked';
      break;
    }

  }
}