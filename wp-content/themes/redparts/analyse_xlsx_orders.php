<?php
/**
 * Fichier lecture gestion order se stallentis
 */
require_once('./stellantisOrder/services/OrderFromExcelFile.php');
require_once('./stellantisOrder/services/MySqlOrderRepository.php');
require_once('./stellantisOrder/exceptions/FileNotFindException.php');
require_once '/home/mdwfrkglvc/www/wp-content/themes/redparts/stellantisOrder/helpers/OrderHelper.php';
require_once('./stellantisOrder/html/DisplayOrder.php');

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
		$orderHelper = new OrderHelper();

		$orderSource = new OrderFromExcelFile($filename, $orderHelper);
		$orderRepository = new MySqlOrderRepository();
		$displayOrder = new DisplayOrder();
		
		// Lecture des données
		$orderSource->readOrderSourceData();		

		// Récupération des commandes
		$orders = $orderSource->getOrders();

		// Récupération des commandes dupliquées
		$duplicatedOrders = $orderRepository->findDuplicatedOrder($orders);

		// Récupération des Commandes en erreurs
		$failureOrders = $orderHelper->getFailureOrders();

		// Sauvegarde des commandes
		$orderRepository->save($orders);

		// Creation HTML des commandes
		$displayOrder->setDuplicateAndFailureOrder($duplicatedOrders, $failureOrders);
		$ordersHtml = $displayOrder->createHtmlFromOrders($orders);

		// Renvoie des données 
		$data['result'] = $ordersHtml;
		$data['orders'] = $orderSource->getOrdersBis();
		$data['duplicatedOrder'] = $duplicatedOrders;
		$data['failureOrders'] = [];

		echo(json_encode($data));
	}
	catch(\Throwable $th) {
		echo($th->getMessage());
	}
}