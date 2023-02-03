<?php

function trouver_date($numSemaine, $annee)
{
	$timeStampPremierJanvier = strtotime($annee . '-01-01');
	$jourPremierJanvier = date('w', $timeStampPremierJanvier);

	//-- recherche du N° de semaine du 1er janvier -------------------
	$numSemainePremierJanvier = date('W', $timeStampPremierJanvier);

	//-- nombre à ajouter en fonction du numéro précédent ------------
	$decallage = ($numSemainePremierJanvier == 1) ? $numSemaine - 1 : $numSemaine;
	//-- timestamp du jour dans la semaine recherchée ----------------
	$timeStampDate = strtotime('+' . $decallage . ' weeks', $timeStampPremierJanvier);
	//-- recherche du lundi de la semaine en fonction de la ligne précédente ---------
	$jourDebutSemaine = ($jourPremierJanvier == 1) ? date('Y-m-d', $timeStampDate) : date('Y-m-d', strtotime('last monday', $timeStampDate));
	return	$jourDebutSemaine;
}

function FindCalage($lang)
{
	$lang = strtoupper($lang);
	if($lang == "HE" ||	$lang == "AR" || $lang == "ZH")
	{
		return "C2";
	}
	else if($lang == "EN")
	{
		return "C3";
	}
	else if($lang == "ENVC")
	{
		return "C4";
	}
	else {
		return "C1";
	}

}

function MAJtabOrder($orderDate)
{
	$tabFileName = array();
	$newOrderDate = array();
	$x=0;
	foreach ($orderDate as $key => $value) {
		$cond = true;
		for($y=0;$y<count($tabFileName);$y++) {
			if($tabFileName[$y] == $value["fileName"])
			{
				$newOrderDate[$y]["quantity"] += $value["quantity"];
				$cond = false;
				break;
			}
		}

		if($cond){
			$tabFileName[$x]=$value["fileName"];
			$newOrderDate[$x]["partNumber"] = $value["partNumber"];
			$newOrderDate[$x]["fileName"] = $value["fileName"];
			$newOrderDate[$x]["quantity"] = $value["quantity"];
			$x++;
		}

	}

	return $newOrderDate;
}





setlocale(LC_TIME, "fr_FR");

require('/home/mdwfrkglvc/www/wp-config.php');
global $wpdb;
error_reporting(E_ALL);




$tabOrder = array();

$tabOrder[0]["partNumber"] = "23AMOKKARXX30L2";
$tabOrder[0]["fileName"] = "OCRCAOM2302ar";
$tabOrder[0]["quantity"] = 500;
$tabOrder[1]["partNumber"] = "23AMOKKDEXX30L2";
$tabOrder[1]["fileName"] = "OCRCAOM2302de";
$tabOrder[1]["quantity"] = 514;
$tabOrder[2]["partNumber"] = "23AMOKKENFR30L2";
$tabOrder[2]["fileName"] = "OCRCAOM2302en";
$tabOrder[2]["quantity"] = 254;
$tabOrder[3]["partNumber"] = "23AMOKKENFR30L2";
$tabOrder[3]["fileName"] = "OCRCAOM2302fr";
$tabOrder[3]["quantity"] = 254;
$tabOrder[4]["partNumber"] = "23AMOKKFRXX30L2";
$tabOrder[4]["fileName"] = "OCRCAOM2302fr";
$tabOrder[4]["quantity"] = 100; //Doit être additionné avec l'ind 3

$orderDate = "2023-02-01";
$deliveryDate = "2023-03-01";

$result = CreateXlsOrder("Quick Guide",  $orderDate, $deliveryDate ,$tabOrder);
echo $result;

function CreateXlsOrder($docType, $orderDate, $deliveryDate ,$tabOrder)
{
	setlocale(LC_TIME, "fr_FR");
	$todayTime = date("Y-m-d-H-i-s");

	if(count($tabOrder)<=0)
	return "Error : No data";

	$tabOrder = MAJtabOrder($tabOrder); //Pour mettre à jour le tableau si des fileName Sont identiques

	if(count($tabOrder)<=0)
	return "Error : No data";

	$filename = "/home/mdwfrkglvc/www/wp-content/uploads/models/BDC-MODEL-MILLAU.xlsx";

	if (file_exists($filename)==false)
	{
		return("Error : Model file not exist");
	}


	//////////////////// OUVERTURE FICHIER //////////////////////////


	$filenamerequire = "/home/mdwfrkglvc/www/wp-content/themes/redparts/PHPExcel-1.8/Classes/PHPExcel.php";
	if (file_exists($filenamerequire)) {
		require_once $filenamerequire;
	}
	else {
		return ("Error : Librairy file not exist");
	}

	$excel2 = null;
	// Chargement du fichier Excel

	$excel2 = PHPExcel_IOFactory::load($filename);


	$excel2 = PHPExcel_IOFactory::createReaderForFile($filename);
	$excel2 = $excel2->load($filename);



	///////////////////Placement sur la feuille BDC (3em feuille) ///////////////////////////////
	$excel2->setActiveSheetIndex(2);



	$fileCodeName = substr($tabOrder[0]["fileName"],0,strlen($tabOrder[0]["fileName"])-2);
	$excel2->getActiveSheet()->setCellValue('E4', $fileCodeName)
	->setCellValue('E6', $docType);

	$date = new DateTime($deliveryDate);
	$deliveryDate = $date->format('d/m/Y');

	$date = new DateTime($orderDate);
	$orderDate = $date->format('d/m/Y');

	$excel2->getActiveSheet()->setCellValue('E17', $orderDate)
	->setCellValue('E23', $deliveryDate	);

	$x=0;
	$xC1 = 0;
	$xC2 = 0;
	$xC3 = 0;
	$xC4 = 0;
	foreach ($tabOrder as $key => $value) {
		$Cal = FindCalage(substr($value['fileName'],strlen($value['fileName'])-2,2));
		if($Cal == "C1")
		{
			$ind = 96+$xC1;
			$xC1++;
		}
		if($Cal == "C2")
		{
			$ind = 125+$xC2;
			$xC2++;
		}
		if($Cal == "C3")
		{
			$ind = 130+$xC3;
			$xC3++;
		}
		if($Cal == "C4")
		{
			$ind = 131+$xC4;
			$xC4++;
		}


		$excel2->getActiveSheet()->setCellValue('C'.($ind), substr($value['fileName'],strlen($value['fileName'])-2,2)); //langue
		$excel2->getActiveSheet()->setCellValue('D'.($ind), $value['fileName']); //fileName
		$excel2->getActiveSheet()->setCellValue('E'.($ind), $docType);
		$excel2->getActiveSheet()->setCellValue('I'.($ind), $value['quantity']);



		$x++;
	}



	// SAUVEGARDE
	$fileNameSaved = "/home/mdwfrkglvc/www/wp-content/uploads/orders/orders/".$docType."_".$fileCodeName."_".$todayTime.".xlsx";
	$objWriter = PHPExcel_IOFactory::createWriter($excel2, 'Excel2007');
	$objWriter->save($fileNameSaved);
	$excel2->disconnectWorksheets();
	unset($objWriter, $excel2);

	return ($fileNameSaved);


}







?>
