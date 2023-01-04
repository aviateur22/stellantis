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



function addOrder($id_order,$par_qui,$orderBuyer,$countryName,$countryCode,$partNumber,$codePochette,$quantity,$deliveredDate,$wip,$lien_couv,$family,$week,$year,$shortTerm)
{
	$dataOuput = array('info' => "New order" ,'idOldOrder'=>0 );
	global $wpdb;
	//Check if project exist
	$_sql = "SELECT * FROM orders WHERE deliveredDate='".$deliveredDate."' AND family='".$family."' AND countryCode='".$countryCode."'
	AND partNumber='".$partNumber."' AND codePochette='".$codePochette."' AND wip<>'READY' AND shortTerm=".$shortTerm;
	$tabResultsorder = $wpdb->get_results($_sql, ARRAY_A);
	if($wpdb->num_rows == "0"){
		//Cette commande existe pas
		if($quantity > 0)
		{
			$dataOuput['info'] = "New order";
		}
		else {
			$dataOuput['info'] = "";
		}
	}
	else{ //Cette commande existe, on mémorise l'ID pour les supprimer ensuite
		$dataOuput['idOldOrder'] = $tabResultsorder[0]['id'];


		if($tabResultsorder[0]['quantity'] == $quantity)
		$dataOuput['info'] = "Order update (same quantity)";
		else
		$dataOuput['info'] = "Order update (old quantity = ". $tabResultsorder[0]['quantity'].")";

	}
	if($quantity > 0){
		$wpdb->insert('orders', array(
			'orderId' => $id_order,
			'orderFrom' => $par_qui,
			'orderBuyer' => $orderBuyer,
			'family' => $family,
			'countryName' => $countryName,
			'countryCode' => $countryCode,
			'partNumber' => $partNumber,
			'codePochette' => $codePochette,
			'quantity' => $quantity,
			'deliveredDate' => $deliveredDate,
			'wip' => $wip,
			'lien_couv'=>$lien_couv,
			'week'=>$week,
			'year'=>$year,
			'shortTerm'=>$shortTerm,
		));
	}

	return $dataOuput;

}
setlocale(LC_TIME, "fr_FR");

require('/home/mdwfrkglvc/www/wp-config.php');
global $wpdb;

error_reporting(E_ALL);

$user_info = wp_get_current_user();
$par_qui = $user_info->first_name . " " . $user_info->last_name;

$filename = $_POST['filename'];
$id_order = 0;

$data = array('result'=>'');
if(isset($filename)){
	//////////////////// ANALYSE FICHIER //////////////////////////

	$tabcolVide = array();
	$data['redRows'] = array();

	$filename = "/home/mdwfrkglvc/www/wp-content/uploads/orders/".$filename;
	if(!file_exists($filename))
	{
		echo "Error : File is not valid";
		return;
	}

	$fileNotValid = "<div style='background-color:#FFFFFF;padding:20px;border-radius:5px;'><p style='color:red;text-align:center'>
	<strong>Excel file is not conform. Please check the workbook and retry.</strong></p></div>";



	$filenamerequire = "/home/mdwfrkglvc/www/wp-content/themes/redparts/PHPExcel-1.8/Classes/PHPExcel.php";
	if (file_exists($filenamerequire)) {
		require_once $filenamerequire;
	}
	else {
		return null;
	}

	$objPHPExcel = null;
	// Chargement du fichier Excel

	$objPHPExcel = PHPExcel_IOFactory::load($filename);


	$excelReader = PHPExcel_IOFactory::createReaderForFile($filename);
	$excelObj = $excelReader->load($filename);
	//$worksheet = $excelObj->getSheet(0);
	$worksheet = $excelObj->getSheetNames();
	$data['result'] = "<p>";
	$s = -1;
	$l = -1;
	foreach ($worksheet as $key => $value) {
		if(stristr(strtoupper($value),"WEEKLY") || stristr(strtoupper($value),"LONG") || stristr(strtoupper($value),"SHORT")|| stristr(strtoupper($value),"DAILY")){
			$data['result'] .= "Detected sheet : <strong>" . $value . "</strong> : <span style='color:green'>Analysed</span><br>";
			if (stristr(strtoupper($value),"SHORT")|| stristr(strtoupper($value),"DAILY")){
				$s=$key;
			}
			else if (stristr(strtoupper($value),"WEEKLY")|| stristr(strtoupper($value),"LONG")){
				$l=$key;
			}
		}
		else {
			$data['result'] .= "Detected sheet : <strong>" . $value . "</strong> : <span style='color:red'>Not analysed</span><br>";
		}
	}
	$data['result'] .= "</p>";

	if ($l < 0 || $s < 0)
	{
		$data['result'] = $fileNotValid;
		echo json_encode($data);
		return;
	}

	$data['result'] .= "<div id='menu'><ul id='onglets'>";
  $data['result'] .= "<li onclick='changeOnglet(0);' id='short-term-li' class='active'><a > Short Term </a></li>";
  $data['result']  .= "<li onclick='changeOnglet(1);' id='long-term-li'><a > Long Term </a></li>";
  $data['result'] .= "</ul></div>";

	$sidOldOrder = ""; //Variable qui sert à mémoriser les orders à supprimer (ids séparés par ,)
	$data['result'] .= "<div id='long-term' style='display:none;padding:10px;background-color:#FFF;border:solid 1px;border-radius:5px;height:90vh;width:100%;overflow-x: auto;overflow-y: auto;white-space: nowrap;'>";
	$data['result'] .= "<table class='tableaulong'><thead>";
	$id_order = rand();
	$condRefExistePAS = false;
	$condRedRow = false;
	$rEntete = 200;
	$cPartNumber = -1;
	$cCountryName=-1;
	$cCountryCode=-1;
	$cFamily=-1;
	$cCodePochette = 200;
	$partNumber = "";
	$countryName="";
	$family="";
	$codePochette = "";
	$r = 1;


	///////////////////ANALYSE DE LA FEUILLE LONG TERME ///////////////////////////////
	$sheet = $objPHPExcel->getSheet($l);

	// On boucle sur les lignes
	foreach($sheet->getRowIterator() as $row) {
		$data['result'] .= "<tr id='row-".$r."' style='background-color:%COLOR%'><td>%BTN%</td>";
		$CondAjoutRow = true;

		$x = 1;
		// On boucle sur les cellules
		foreach ($row->getCellIterator() as $cell) {
			$CondAjout = $CondAjoutRow;
			if($x>2 && array_search($x,$tabcolVide)===false) {
				$value = $cell->getCalculatedValue();
				if ($value == "#N/A")
				$value = $cell->getOldCalculatedValue();

				if($cPartNumber == $x){
					$partNumber = $value;

					if(strlen($value)>= 11 && preg_match('/[A-Za-z]/', $value) && preg_match('/[0-9]/', $value))
					{

						$newvalue = substr($value,0,7) . "<span style='color:blue'>" . substr($value,7,2) . "</span>";
						if(stristr(strtoupper(substr($value,9,2)),"XX") == false)
						$newvalue.="<span style='color:yellow'>" . substr($value,9,2) . "</span>";
						else
						$newvalue.=substr($value,9,2);

						$newvalue.=substr($value,11,strlen($value)-11);
						$value=$newvalue;
					}
					else if(strlen($value)< 2)
					{
						$CondAjoutRow = false;
					}
					else {
						//On recompose le part Number
						//ATTENTE TABLE DE CORRESPONDANCE
					}

					//Détection si on a la référence en stock
					if($r == 4 || $r == 9 ) // CECI EST UN TEST
					{
						$condRefExistePAS = true;
					}
					else {
						$condRefExistePAS = false;
					}
				}
				if($x > $cPartNumber) // Quantité
				{
					if($value == "")
					$value = 0;
				}

				$CondAjout = true;
			}
			else {
				$CondAjout = false;
			}
			if(stristr(strtoupper($value),"PART") != false && stristr(strtoupper($value),"NUMBER") != false)
			{
				$rEntete = $r;
				$cPartNumber = $x;
			}

			if(stristr(strtoupper($value),"CODE") != false && stristr(strtoupper($value),"POCHETTE") != false)
			{
				$rEntete = $r;
				$cCodePochette = $x;
			}

			if(stristr(strtoupper($value),"NAME") != false && stristr(strtoupper($value),"COUNTRY") != false)
			{
				$rEntete = $r;
				$cCountryName = $x;
			}

			if(stristr(strtoupper($value),"COUNTRY") != false && stristr(strtoupper($value),"NAME") == false)
			{
				$rEntete = $r;
				$cCountryCode = $x;
			}

			if(stristr(strtoupper($value),"FAMILY") != false)
			{
				$rEntete = $r;
				$cFamily = $x;
			}


			if($r > 1 && $r == $rEntete && ($value == "" || $value === "0")){
				$CondAjout = false;
				array_push($tabcolVide,$x);
			}
			if($r==$rEntete && $x>$cPartNumber && $value > 0 && $value < 54)
			{
				$tabWeek[$x] = intval($value);
			}


			if($r==2){
				if (intval(substr($value,0,4)) >= 2022)
				{
					$tabYear[$x] = intval(substr($value,0,4));
				}
				else if(count($tabYear) > 0){
					if($tabYear[$x-1] > 0)
					$tabYear[$x] = $tabYear[$x-1];
				}
			}


			if($r < 3)
			{
				if($value == "0")
				$value = "";
			}

			$tab_info_id = array();
			if($r > $rEntete){
				if($cCountryCode == $x){$countryCode = $value;}
				else if($cCountryName == $x){$countryName = $value;}
				else if($cCodePochette == $x){$codePochette = $value;}
				else if($cFamily == $x){$family = $value;}
				else if ($x > $cCodePochette)
				{

					//$datesem = trouver_date($tabWeek[$x], $tabYear[$x], 1);
					if(array_key_exists($x, $tabWeek) && array_key_exists($x, $tabYear)){
						$datesem = trouver_date($tabWeek[$x], $tabYear[$x]);
						$tab_info_id= addOrder($id_order,$par_qui,"STELLANTIS",$countryName,$countryCode,$partNumber,$codePochette,$value,$datesem,"PREPARATION","https://mdw-05.fr/wp-content/uploads/2021/01/307.png",$family,$tabWeek[$x],$tabYear[$x],0);
						if($tab_info_id['idOldOrder'] > 0 )
						$sidOldOrder .= $tab_info_id['idOldOrder'] . ",";
					}
				}
			}

			// if(!$CondAjout || !$CondAjoutRow)
			// $value = "";

			if($CondAjout && $CondAjoutRow)
			{
				$textInfo = $value;

				if($x > $cPartNumber) // Quantité
				{
					$info = $tab_info_id['info'];

					if($info == "New order")
					$textInfo ="<p class='infobulle' style='color:black;font-weight:bold;'>" . $value . "<span>".$info."</span></p>";
					else if ($info == "Order update (same quantity)")
					$textInfo ="<p class='infobulle' style='color:grey;font-weight:bold;'>" . $value . "<span>".$info."</span></p>";
					else if($info != "")
					$textInfo ="<p class='infobulle'  style='color:blue;font-weight:bold;'>" . $value . "<span>".$info."</span></p>";
				}
				$data['result'] .= "<td>" . $textInfo . "</td>";

			}


			$x++;

		}
		$data['result'] .= "</tr>";

		if($r > 1 && $r == $rEntete)
		$data['result'] .= "</thead><tbody>";


		$r++;

		//Détection si on a la référence en stock
		if($condRefExistePAS)
		{
			array_push($data['redRows'],($r-1));
			$data['result'] = str_replace("%BTN%","<button class='del-btn-ico' onclick='delRow(".($r-1).");' id='del-".($r-1)."'></button>",$data['result']);
			$data['result'] = str_replace("%COLOR%","#FF5733",$data['result']);
			$condRedRow = true;
		}
		else {


			$data['result'] = str_replace("%BTN%","",$data['result']);
			$data['result'] = str_replace("%COLOR%","#FFF",$data['result']);
		}
	}

	$data['result'] .= "</tbody></table>";
	$data['result'] .= "</div>";






	//////////////////// ANALYSE COURT TERME ///////////////////
	$data['result'] .= "<div id='short-term' style='padding:10px;background-color:#FFF;border:solid 1px;border-radius:5px;height:90vh;width:100%;overflow-x: auto;overflow-y: auto;white-space: nowrap;'>";
	$data['result'] .= "<table class='tableaulong'><thead>";
	$id_order = rand();
	$condRefExistePAS = false;
	$rEntete = 200;
	$cPartNumber = -1;
	$cCountryName=-1;
	$cCountryCode=-1;
	$cFamily=-1;
	$cCodePochette = 200;
	$partNumber = "";
	$countryName="";
	$family="";
	$codePochette = "";
	$tabDate = array();
	$r = 1;


	$sheet = $objPHPExcel->getSheet($s);

	// On boucle sur les lignes
	foreach($sheet->getRowIterator() as $row) {
		$data['result'] .= "<tr id='row-".($r+1000)."' style='background-color:%COLOR%'><td>%BTN%</td>";
		$CondAjoutRow = true;

		$x = 1;
		// On boucle sur les cellules
		foreach ($row->getCellIterator() as $cell) {
			$CondAjout = $CondAjoutRow;
			if($x>2 && array_search($x,$tabcolVide)===false) {
				$value = $cell->getCalculatedValue();
				if ($value == "#N/A")
				$value = $cell->getOldCalculatedValue();

				if($cPartNumber == $x){
					$partNumber = $value;

					if(strlen($value)>= 11 && preg_match('/[A-Za-z]/', $value) && preg_match('/[0-9]/', $value))
					{



						$newvalue = substr($value,0,7) . "<span style='color:blue'>" . substr($value,7,2) . "</span>";
						if(stristr(strtoupper(substr($value,9,2)),"XX") == false)
						$newvalue.="<span style='color:yellow'>" . substr($value,9,2) . "</span>";
						else
						$newvalue.=substr($value,9,2);

						$newvalue.=substr($value,11,strlen($value)-11);
						$value=$newvalue;
						$CondAjout = true;

					}
					else if(strlen($value)< 2)
					{
						$CondAjoutRow = false;
						$CondAjout = false;

					}
					else {
						//On recompose le part Number
						//A FAIRE

					}
					//Détection si on a la référence en stock
					if($r == 4 || $r == 9 )
					{
						$condRefExistePAS = true;
					}
					else {
						$condRefExistePAS = false;
					}
				}


				if($x > $cPartNumber) // Quantité
				{
					if($value == "")
					$value = 0;
				}

			}
			else {
				$CondAjout = false;
			}
			if(stristr(strtoupper($value),"PART") != false && stristr(strtoupper($value),"NUMBER") != false)
			{
				$rEntete = $r;
				$cPartNumber = $x;
			}

			if(stristr(strtoupper($value),"CODE") != false && stristr(strtoupper($value),"POCHETTE") != false)
			{
				$rEntete = $r;
				$cCodePochette = $x;
			}

			if(stristr(strtoupper($value),"NAME") != false && stristr(strtoupper($value),"COUNTRY") != false)
			{
				$rEntete = $r;
				$cCountryName = $x;
			}

			if(stristr(strtoupper($value),"COUNTRY") != false && stristr(strtoupper($value),"NAME") == false)
			{
				$rEntete = $r;
				$cCountryCode = $x;
			}

			if(stristr(strtoupper($value),"FAMILY") != false)
			{
				$rEntete = $r;
				$cFamily = $x;
			}


			if($r > 1 && $r == $rEntete && ($value == "" || $value === "0")){
				$CondAjout = false;
				array_push($tabcolVide,$x);
			}
			if($r==$rEntete && $x>$cPartNumber){
				if( strlen($value) >= 10 && stristr($value,"/") != false)
				{
					$tabDate[$x] = substr($value,6,4)."-".substr($value,3,2)."-".substr($value,0,2);
				}
				else if($value > 36500 && $value < 50000){
					$tabDate[$x] = \PHPExcel_Style_NumberFormat::toFormattedString($value, 'YYYY-MM-DD');
					$value = \PHPExcel_Style_NumberFormat::toFormattedString($value, 'DD/MM/YYYY');
				}
			}



			if($r < 3)
			{
				if($value == "0")
				$value = "";
			}

			$tab_info_id = array();
			if($r > $rEntete){
				if($cCountryCode == $x){$countryCode = $value;}
				else if($cCountryName == $x){$countryName = $value;}
				else if($cCodePochette == $x){$codePochette = $value;}
				else if($cFamily == $x){$family = $value;}
				else if ($x > $cCodePochette)
				{

					if(array_key_exists($x, $tabDate)){
						$datesem = $tabDate[$x];
						$good_format=strtotime ($datesem);
						$deliveredWeek = date('W',$good_format);

						$tab_info_id= addOrder($id_order,$par_qui,"STELLANTIS",$countryName,$countryCode,$partNumber,$codePochette,$value,$datesem,"PREPARATION","https://mdw-05.fr/wp-content/uploads/2021/01/307.png",$family,$deliveredWeek,substr($tabDate[$x],6,4),1);
						if($tab_info_id['idOldOrder'] > 0 )
						$sidOldOrder .= $tab_info_id['idOldOrder'] . ",";
					}
				}
			}


			if($CondAjout && $CondAjoutRow)
			{
				$textInfo = $value;

				if($x > $cPartNumber) // Quantité
				{
					$info = $tab_info_id['info'];

					if($info == "New order")
					$textInfo ="<p class='infobulle' style='color:black;font-weight:bold;'>" . $value . "<span>".$info."</span></p>";
					else if ($info == "Order update (same quantity)")
					$textInfo ="<p class='infobulle' style='color:grey;font-weight:bold;'>" . $value . "<span>".$info."</span></p>";
					else if($info != "")
					$textInfo ="<p class='infobulle'  style='color:blue;font-weight:bold;'>" . $value . "<span>".$info."</span></p>";
				}
				$data['result'] .= "<td>" . $textInfo . "</td>";

			}


			$x++;

		}
		$data['result'] .= "</tr>";

		if($r > 1 && $r == $rEntete)
		$data['result'] .= "</thead><tbody>";


		$r++;

		//Détection si on a la référence en stock
		if($condRefExistePAS)
		{
			array_push($data['redRows'],($r-1+1000));
			$data['result'] = str_replace("%BTN%","<button class='del-btn-ico' onclick='delRow(".($r-1+1000).");' id='del-".($r-1)."'></button>",$data['result']);
			$data['result'] = str_replace("%COLOR%","#FF5733",$data['result']);
			$condRedRow = true;
		}
		else {


			$data['result'] = str_replace("%BTN%","",$data['result']);
			$data['result'] = str_replace("%COLOR%","#FFF",$data['result']);
		}
	}
	$data['sidOldOrder'] = $sidOldOrder;
	$data['tabDate'] = $tabDate;

	$data['result'] .= "</tbody></table>";
	$data['result'] .= "</div>";

	if($condRedRow){
		$data['result'] .= "<div id='red-row-div' style='text-align:center;margin-top:10px;padding:10px;background-color:#FF5733;border-radius:5px'>Part Number documentation disable. Please remove red row(s) to confirm your order.</div>";
		$data['result'] .= "<div style='margin-top:30px;'><button id='btn-confim-order' onclick='orderConfirm(".$id_order.");' class='acceptButton' disabled>Confirm print order</button></div>";
	}
	else {
		$data['result'] .= "<div style='margin-top:30px;'><button onclick='orderConfirm(".$id_order.");' class='acceptButton'>Confirm print order</button></div>";
	}



}
else {
	echo "Error : File is not valid";
	return;
}

echo json_encode($data); // on renvoie en JSON les résultats





?>
