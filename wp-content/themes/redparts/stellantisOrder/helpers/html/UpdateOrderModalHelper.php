<?php
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/model/User.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/utils/validators.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/utils/StaticData.php';

// Renvoie la modal a charger pour modification d'une commande
class UpdateOrderModalHelper {

  /**
   * Utilisateur
   *
   * @var User
   */
  protected User $user;

  function __construct(User $user) {
    $this->user = $user;
  }

  /**
   * Popup modification commandes
   *
   * @return string
   */
  function updateOrderModal(): string {

    $statusSelectOption = !isUserRoleFindInArrayOfRoles($this->user, StaticData::FACTORY_STELLANTIS_ROLES_NAMES) ?
      $this->setPopupSelectectOption() :
      '';

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
          <div class="update__information">
            <div class="status__information">
              <div class="group__control">
                <label class="text--strong text--left" for="processedWith"> Being processed at </label>
                <p id="processedWith">XXXX</p>
              </div>
              <div class="group__control">
                <label class="text--strong text--left" for="statusLabel"> Actual status </label>
                <p id="statusLabel">XXXX</p>
              </div>
            </div>
            
            <div class="group__control">
              <label class="text--strong text--right" for="quantity">PartNumber</label>
              <p id="displayPartNumber">XXXX</p>
            </div>
          </div>
          <div class="update__car__information">
            <div class="group__control">
              <label class="text--strong text--left" for="coverCode">Cover Code</label>
              <p id="coverCode">XXXX</p>
            </div>
            <div class="group__control">
              <label class="text--strong text--right" for="brand">Car Brand</label>
              <p id="brand">XXXX</p>
            </div>
          </div>          
          <div class="group__control">
            <label class="text--strong text--left" for="coverLink">Cover Link</label>
            <p id="coverLink">XXXX</p>
          </div>
          <div class="update__information">
            <div class="group__control">
              <label class="text--strong text--left" for="quantity">Quantity</label>
              <input min="1" id="displayQuantity" name="quantity" id="quantity" type="number">
            </div>
            <div class="group__control">
              <label class="text--strong text--left" for="deliveredDate">Delivered Date</label>
              <input id="displayDeliveredDate" name="deliveredDate" id="deliveredDate" type="date">
            </div>
          </div>
        '.$statusSelectOption.'
        </div>
        <div class="modal__button__container">
          <div>          
            <button type="submit" class="modal__button" value> Update Order </button>
            <button onclick="hideUpdateOrder();" type="button" class="modal__button cancel--button" value> Non </button>
          </div>
        </div>
      </form>
    <div>';
  }

  /**
   * Renvoie du HTML SELECT Input de la modal de modification d'une commande
   *
   * @return string
   */
  private function setPopupSelectectOption(): string {
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
        <select id="displayStatus" name="status" id="status" required>
            <option value="">--Please choose an option--</option>
            <option value='.StaticData::ORDER_STATUS['PREFLIGHT_ST'].'>'.StaticData::STATUS_DISPLAY_NAME_ROLE_ST_MI_MA['PREFLIGHT'].'</option>
            <option value='.StaticData::ORDER_STATUS['ON_PROGRESS_ST'].'>'.StaticData::STATUS_DISPLAY_NAME_ROLE_ST_MI_MA['ON_PROGRESS'].'</option>
            <option value='.StaticData::ORDER_STATUS['READY_ST'].'>'.StaticData::STATUS_DISPLAY_NAME_ROLE_ST_MI_MA['READY'].'</option>
            <option value='.StaticData::ORDER_STATUS['DELIVERED_ST'].'>'.StaticData::STATUS_DISPLAY_NAME_ROLE_ST_MI_MA['DELIVERED'].'</option>
            <option value='.StaticData::ORDER_STATUS['BLOCKED_ST'].'>'.StaticData::STATUS_DISPLAY_NAME_ROLE_ST_MI_MA['BLOCKED'].'</option>
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

}