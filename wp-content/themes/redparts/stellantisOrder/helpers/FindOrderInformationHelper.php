<?php
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/interfaces/OrderRepositoryInterface.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/services/MySqlOrderRepository.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/helpers/DisplayOrderColorHelper.php';
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/utils/StaticData.php';

/**
 * Helper recherche commande
 */
class FindOrderInformationHelper {

  /**
   * Order repository
   *
   * @var OrderRepositoryInterface
   */
  protected OrderRepositoryInterface $orderRepository;

  /**
   * Aide à l'affichage des couleurs
   *
   * @var DisplayOrderColorHelper
   */
  protected DisplayOrderColorHelper $displayOrderColorHelper;

  function __construct(OrderRepositoryInterface $orderRepository, DisplayOrderColorHelper $displayOrderColorHelper) {
    $this->orderRepository = $orderRepository;
    $this->displayOrderColorHelper = $displayOrderColorHelper;
  }

  /**
   * Recherche d'une commande
   *
   * @param string $id
   * @return array
   */
  public function findOrder(string $id): array {
    // Recherche commande en base de données
    $order = $this->orderRepository->findOne($id);

    if(count($order) === 0 ) {
      throw new \Exception('Commande non trouvée', 404);
    }
    return (array)$order[0];
  }

  /**
   * Renvoie la société traitant la commmande
   *
   * @param integer $wipId
   * @return string
   */
  public function findProcessedWith(int $wipId): string {
    
    switch($wipId) {
      // Millau
      case in_array($wipId, StaticData::MILLAU_ID, true):
        return StaticData::MILLAU_FACTORY_NAME;
      break;
      
      // Manchecourt
      case in_array($wipId, StaticData::MANCHECOURT_ID):
        return StaticData::MANCHECOURT_FACTORY_NAME;
      break;

      // Stellantis
      case in_array($wipId, StaticData::STELLANTIS_ID):
        return StaticData::STELLANTIS_NAME;
      break;
      default: return StaticData::STELLANTIS_NAME;
    }

  }

  /**
   * Renvoie le nom du statut de la commande
   *
   * @param integer $wipId
   * @return string
   */
  public function getOrderStatusLabel(int $wipId): string {

    switch($wipId) {
      // BEFORE PreFlight
      case in_array($wipId, StaticData::BEFORE_PREFLIGHT_ID):
        return StaticData::STATUS_DISPLAY_NAME_ROLE_ST_MI_MA['BEFORE_PREFLIGHT'];        
      break;

      // PreFlight
      case in_array($wipId, StaticData::PREFLIGHT_ID):
        return StaticData::STATUS_DISPLAY_NAME_ROLE_ST_MI_MA['PREFLIGHT'];        
      break;
      
      // OnProgress
      case in_array($wipId, StaticData::ON_PROGRESS_ID):
        return StaticData::STATUS_DISPLAY_NAME_ROLE_ST_MI_MA['ON_PROGRESS'];        
      break;

      // ready
      case in_array($wipId, StaticData::READY_ID):
        return StaticData::STATUS_DISPLAY_NAME_ROLE_ST_MI_MA['READY'];        
      break;

      // delivered
      case in_array($wipId, StaticData::DELIVERED_ID):
        return StaticData::STATUS_DISPLAY_NAME_ROLE_ST_MI_MA['DELIVERED'];        
      break;

      // BLOCKED
      case in_array($wipId, StaticData::BLOCKED_ID):
        return StaticData::STATUS_DISPLAY_NAME_ROLE_ST_MI_MA['BLOCKED'];        
      break;

      default: 
      return StaticData::STATUS_DISPLAY_NAME_ROLE_ST_MI_MA['BLOCKED'];      
      break;
    }
  }

  /**
   * Renvoie la className du statut de la commande
   *
   * @param integer $wipId
   * @return string
   */
  public function getOrderstatusColorClassName(int $wipId): string {
    // Couleur du statut actuel de la commande
    return $this->displayOrderColorHelper->findColorOrderDisplay($wipId);
  }

}