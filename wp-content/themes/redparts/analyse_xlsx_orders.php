<?php
/**
 * Fichier lecture gestion order se stallentis
 */
require_once('./stellantisOrder/services/OrderFromExcelFile.php');
require_once('./stellantisOrder/services/MySqlOrderRepository.php');
require_once('./stellantisOrder/exceptions/FileNotFindException.php');
require_once('./stellantisOrder/helpers/OrderHelper.php');
require_once('./stellantisOrder/html/DisplayOrder.php');
require_once('./stellantisOrder/helpers/DeleteHelper.php');

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

		// Implementation des modèles
		$orderRepository = new MySqlOrderRepository();
		$orderHelper = new OrderHelper($orderRepository);
		$orderSource = new OrderFromExcelFile($filename, $orderHelper);		
		$displayOrder = new DisplayOrder();
		$deleteHelper = new DeleteHelper($orderRepository);
		
		// Lecture des données
		$orderSource->readOrderSourceData();		

		// Récupération des commandes
		$orders = $orderSource->getOrders();

		// Récupération des commandes en erreur
		$duplicatedOrders = $orderHelper->getDuplicateOrders();
		$failureOrders = $orderHelper->getFailureOrders();
		$quantityErrorOrders = $orderHelper->getErrorQuantityOrders();

		// Sauvegarde des commandes
		$orderRepository->save($orders);

		//Nettoyages des ancienne données
		$deleteHelper->deleteOldgeneratedOrderFile();
		$deleteHelper->deleteUnusedOrders();

		// Creation HTML des commandes
		$displayOrder->setDuplicateAndFailureOrder($duplicatedOrders, $failureOrders, $quantityErrorOrders);
		$ordersHtml = $displayOrder->createHtmlFromOrders($orders);

		// Renvoie des données HTML
		$data['result'] = $ordersHtml;

		// liste des commandes
		$data['orders'] = $orderSource->getOrders();

		// Commandes dupliquées
		$data['duplicatedOrders'] = $duplicatedOrders;

		// Commandes sans quantité
		$data['quantityErrorOrders'] = $quantityErrorOrders;

		// Commande sans CoverLink
		$data['failureOrders'] = [];

		echo(json_encode($data));
	}
	catch(\Throwable $th) {
		echo($th->getMessage());
	}
}