<?php
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/model/Order.php';

/**
 * Affichage des commandes
 */
class DisplayOrder {
  /**
   * Liste des commandes dupliquées
   *
   * @var array
   */
  protected array $duplicatedOrders;

  /**
   * Liste des commandes en erreur
   * 
   * @var array
   */
  protected array $failureOrders;


  /**
   * Récupération liste commande dupliqué et erreur
   *
   * @param array $duplicatedOrders
   * @param array $failureOrders
   * @return void
   */
  function setDuplicateAndFailureOrder(array $duplicatedOrders, array $failureOrders) {
    $this->duplicatedOrders = $duplicatedOrders;
    $this->failureOrders = $failureOrders;
  }
  
  /**
   * Création du HTML pour afficher les ordres
   *
   * @param array orders - Les commandes
   * @param array $duplictedOrders - Si commande dupliqué
   * @param array $failureOrders - Si commandes en echec
   * 
   * @return string  - Html
   */
  function createHtmlFromOrders(array $orders) {    
    $html = '<div><table class="order-table">';

    // Id de la commande
    $orderId = -1;

    // Header
    $html .= '<thead>';
    $html .= '<tr>';
    $html .= '<th></th>';    
    $html .= '<th> PartNumber </th>';
    $html .= '<th> Qauntité </th>';    
    $html .= '<th> Code pochette </th>';
    $html .= '<th> Date de livraison </th>';
    $html .= '</tr>';
    $html .= '</thead>';

    // Body
    $html .= '<tbody>';
    foreach($orders as $order) {      
      if($order instanceof Order) {
        if($orderId === -1 ){
          $orderId = $order->getOrderId();
        }
        // class pour les <tr>
        $trClass = $this->isOrderValid($order->getPartNumber()) ? 'order-valid' : 'order-failure';        
        $html .= "<tr data-order-id=".$orderId." data-part-number=".$order->getPartNumber()." data-delivered-date=".$order->getdeliveredDate()." class='".$trClass." order-row'>";
        $html .= "<td><button class=del-btn-ico onclick='displayOrderDeleteModal(this);' id='del-".$order->getPartNumber()."'></button></td>";
        //$html .= "<td><button class=".$this->isOrderValid($order->getPartNumber()) ? 'order-failure' : 'del-btn-ico'."' onclick='delRow();' id='del-".$order->getPartNumber()."'></button></td>";
        $html .= $this->createHtmlForOrderProperty($order->getPartNumber());
        $html .= $this->createHtmlForOrderProperty($order->getQuantity());        
        $html .= $this->createHtmlForOrderProperty($order->getCoverCode());
        $html .= $this->createHtmlForOrderProperty($order->getdeliveredDate());
        $html .= '</tr>'; 
      }
           
    }
    $html .='</tbody></table>';

    $html .= $this->addButtonAction($orderId);

    $html .='</div>';

    // Ajout de la modal
    $html .= $this->addConfirmationModal();

    //$this->orderHtml = $html;
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
   * Ajout des Buttons
   *
   * @param string $html - Html a renvoyer
   * @param string $orderId - Id de la commande
   * 
   * @return string - Html
   */
  function addButtonAction(string $orderId): string {

    // Html des buttons
    $html = '';

    if(count($this->duplicatedOrders) > 0 || count ($this->failureOrders) > 0) {
      $html .= "<div id='red-row-div' style='text-align:center;margin-top:10px;padding:10px;background-color:#FF5733;border-radius:5px'>Part Number documentation disable or duplicted Orders. Please remove red row(s) to confirm your order.</div>";
      $html .= "<div style='margin-top:30px;'><button data-order-id=".$orderId." id='btn-confim-order' onclick='orderConfirm(this);' class='acceptButton' disabled>Confirm print order</button></div>";
    } else {
      $html .= "<div style='margin-top:30px;'><button data-order-id=".$orderId." onclick='orderConfirm(this);' class='acceptButton'>Confirm print order</button></div>";
    }

    return $html;
  }

  /**
   * Undocumented function
   *
   * @param string $partNumber
   * @return string
   */
  function addConfirmationModal(): string {
    return '<div id="orderModal" class="modal__container">
      <div class="modal">
        <h4 class="modal__title">Confirmation suppression commande</h4>
        <p class="modal__text">Confirmez-vous la suppression de la commande ?</p>
        <div class="modal__button__container">
          <div>          
            <button onclick="delRow();" type="button" class="modal__button" value> Oui </button>
            <button onclick="hideOrderDeleteModal();" type="button" class="modal__button cancel--button" value> Non </button>
          </div>
        </div>
      </div> 
    </div>';
  }

  /**
   * Détermination de la class background pour les <tr>
   *
   * @param string $partNumber
   * 
   * @return string
   */
  private function isOrderValid(string $partNumber): bool {
    // Commandes en erreur
    $isOrderFailer = in_array($partNumber, $this->failureOrders);

    // Commande dupliqué
    $isOrderduplicate = in_array($partNumber, $this->duplicatedOrders);

    if($isOrderduplicate || $isOrderFailer) {
      return false;
    }
    return true;
  }
}