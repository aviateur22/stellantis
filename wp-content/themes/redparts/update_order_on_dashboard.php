<?php
require_once('./stellantisOrder/utils/StaticData.php');
require_once('./stellantisOrder/helpers/UserHelper.php');
require_once('./stellantisOrder/model/User.php');
require_once('./stellantisOrder/model/updateOrder/UpdateOrderModel.php');
require_once('./stellantisOrder/model/updateOrder/MFAndOtherUpdateOrder.php');
require_once('./stellantisOrder/model/updateOrder/StellantisFactoryUpdateOrder.php');
require_once('./stellantisOrder/model/RepositoriesModel.php');
require_once('./stellantisOrder/utils/RepositorySelection.php');


/**
 * Update des données d'une commande
 */

 error_reporting(E_ALL);

 try {

    /**
    * Id de la commande
    */
    $orderId = $_POST['id'];
    $quantity = $_POST['quantity'];
    $deliveredDate = $_POST['deliveredDate'];
    $status = $_POST['status'];
    

    if(empty($orderId) || empty($quantity) || empty($deliveredDate)) {
      throw new \Exception('Update impossible: Missing required order informations', 400);
    }

    // User
    $userHelper = new UserHelper();
    $user = $userHelper->getUser();

    // Si utilisateur Maury ou autre alors vérification de la disponibilité du statut
    if(!isUserRoleFindInArrayOfRoles($user, StaticData::FACTORY_STELLANTIS_ROLES_NAMES)) {     
      if(empty($status)) {
        throw new \Exception('Update impossible: Missing required order informations', 400);
      }
    }   
    
    // TODO a enlever
    // $orderRepository = new MySqlOrderRepository();

    // Repositories
    $repositorySelection = new RepositorySelection(StaticData::REPOSITORY_TYPE_MYSQL);
    $repositories = $repositorySelection->selectRepositories($user);

    // Model pour mettre à jour une commande
    $updateOrderModel = isUserRoleFindInArrayOfRoles($user, StaticData::FACTORY_STELLANTIS_ROLES_NAMES) ?
    
    // Instance pour les usine de stellantis (WipId absent des données)
    new StellantisFactoryUpdateOrder($repositories, $orderId, $deliveredDate, $quantity) :

    // Autres
    new MFAndOtherUpdateOrder($repositories, $orderId, $deliveredDate, $quantity, $status);
   
    // Mise a jour de la commande
    $updateOrderModel->updateOrder();
    
    // Récupérationd de la commande mise a jour 
    $updatedOrder = $updateOrderModel->findUpdatedOrder();   
    
    // Récupération de la nouveau nom de la class color
    $colorClassName = $updateOrderModel->findClassNameOrderColor($updatedOrder['wipId']);
    $colorClassToRemove = $updateOrderModel->findColorClassToRemove();    
    
    $data['updateOrder'] =  [
      'colorClassName' => $colorClassName,
      'colorClassToRemove' => $colorClassToRemove,
      'deliveredDate' => date('d-M-Y', strtotime($updatedOrder['deliveredDate'])),
      'quantity' => $updatedOrder['quantity']
    ];
   
    echo(json_encode($data));
 } catch (\Throwable $th) {
    // Récupération code HTTP
    $statusCode = $th->getCode() === 0 ? 500 : $th->getCode();

    //Renvoie HTTP Response code
    http_response_code($statusCode);
    
    echo('Error ' . $th->getMessage());
 }