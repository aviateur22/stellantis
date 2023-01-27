<?php
/**
 * Fichier lecture gestion order se stallentis
 */
require_once('./stellantisOrder/services/MySqlOrderRepository.php');
require_once('./stellantisOrder/services/MySqlForecastRepository.php');
require_once('./stellantisOrder/exceptions/FileNotFindException.php');
require_once('./stellantisOrder/helpers/ForecastPrintHelper.php');
require_once('./stellantisOrder/html/DisplayOrder.php');
require_once('./stellantisOrder/helpers/DeleteHelper.php');
require_once('./stellantisOrder/helpers/UserHelper.php');
require_once('./stellantisOrder/helpers/AuthorizeHelper.php');
require_once('./stellantisOrder/exceptions/ForbiddenException.php');
require_once('./stellantisOrder/services/MySqlModelRepository.php');
require_once('./stellantisOrder/helpers/OrderHelper.php');
require_once('./stellantisOrder/services/OrderFromExcelFile.php');
require('/home/mdwfrkglvc/www/wp-config.php');

error_reporting(E_ALL);

$filename = $_POST['filename'];

if(isset($filename)){

	//////////////////// ANALYSE FICHIER //////////////////////////
	try {		
		$filename = "/home/mdwfrkglvc/www/wp-content/uploads/orders/".$filename;

		if(!file_exists($filename))
		{
			throw new FileNotFindException();
		}	

		// Utilisateur
		$userHelper = new UserHelper();
  	$user = $userHelper->getUser();

		// Authorize
		$authorize = new AuthorizeHelper($user);
		$isAuthorizeForAction = $authorize->isUserAuthorizeForNewOrder();

		if(!$isAuthorizeForAction) {
			throw new ForbiddenException();
		}

		// Implementation des modèles
		$orderRepository = new MySqlOrderRepository();
		$modelRepository = new MySqlModelRepository();
		$forecastOrderRepository = new MySqlForecastRepository();
		$forecastPrintHelper = new ForecastPrintHelper($forecastOrderRepository);		
		$displayOrder = new DisplayOrder();
		$deleteHelper = new DeleteHelper($orderRepository);
		$orderHelper = new OrderHelper($orderRepository, $modelRepository, $forecastPrintHelper);
		$orderSource = new OrderFromExcelFile($filename, $orderHelper, $user, $orderRepository);

		// Initialisation lecture fichier
		$orderSource->initializeFile();

		//Nettoyages des ancienne données
		$deleteHelper->deleteOldgeneratedOrderFile();
		$deleteHelper->deleteUnusedOrders();

		// Commandes dupliqués
		$duplicatedOrders = $orderHelper->getDuplicateOrders();

		// Commandes sans Documentation PDF
		$missingCoverLinkOrders = $orderHelper->getMissingCoverLinkOrders();

		// Commandes avec d'autres types erreurs
		$otherErrorOrders = $orderHelper->getOtherErrorOrders();


		// Creation HTML des commandes
		$displayOrder->setDuplicateAndFailureOrder(
			$duplicatedOrders, 
			$missingCoverLinkOrders, 
			$otherErrorOrders
		);
		
		$ordersHtml = $displayOrder->createHtmlFromOrders($orderSource->getOrders());

		// Renvoie des données HTML
		$data['result'] = $ordersHtml;

		// liste des commandes
		$data['orders'] = $orderSource->getOrders();

		// Commandes dupliquées
		$data['duplicatedOrders'] = $duplicatedOrders;

		// Commandes sans quantité
		$data['quantityErrorOrders'] = $otherErrorOrders;

		// Commande sans CoverLink
		$data['failureOrders'] = $missingCoverLinkOrders;

		echo(json_encode($data));

	}
	catch(\Throwable $th) {
		// Récupération code HTTP
    $statusCode = $th->getCode() === 0 ? 500 : $th->getCode();

    //Renvoie HTTP Response code
    http_response_code($statusCode);
    
    echo('Error ' . $th->getMessage());
	}
}