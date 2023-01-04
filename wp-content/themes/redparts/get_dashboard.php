<?php

function dateDiff($date1, $date2){
    $diff = abs($date1 - $date2); // abs pour avoir la valeur absolute, ainsi éviter d'avoir une différence négative
    $retour = array();

    $tmp = $diff;
    $retour['second'] = $tmp % 60;

    $tmp = floor( ($tmp - $retour['second']) /60 );
    $retour['minute'] = $tmp % 60;

    $tmp = floor( ($tmp - $retour['minute'])/60 );
    $retour['hour'] = $tmp % 24;

    $tmp = floor( ($tmp - $retour['hour'])  /24 );
    $retour['day'] = $tmp;

    return $retour;
}

function TrouveIndex($tabarray,$searchValue,$colone)
{
	$numF = 0;
	$keyF = -1;
	foreach ($tabarray as $value) {
		if($value[$colone] == $searchValue)
		{
			$keyF = $numF;
		}
		$numF++;
	}
	return $keyF;
}


setlocale(LC_TIME, "fr_FR");

require('/home/mdwfrkglvc/www/wp-config.php');
global $wpdb;

error_reporting(E_ALL);

$user_info = wp_get_current_user();
$par_qui = $user_info->first_name . " " . $user_info->last_name;




$type = $_POST['type'];
$dateDebut = $_POST['dateDebut'];
$dateFin = $_POST['dateFin'];



$data = array();
$tabResults = array();
if(isset($type) && isset($dateDebut) && isset($dateFin))
{
	$_sql = "SELECT * FROM orders WHERE wip <> 'PREPARATION' AND deliveredDate >= '".$dateDebut."' AND deliveredDate <= '".$dateFin."' ORDER BY deliveredDate";


	$tabResults = $wpdb->get_results($_sql, ARRAY_A);
	if($wpdb->num_rows == "0"){
		$data['orders'] = "<h3 style='color:red;text-align:center;'>No order find</h3>";
		echo json_encode($data); // on renvoie en JSON les résultats
		return;
	}
}
else {
	echo "Erreur : Données d'entrée non reçues";
	return;
}
$O = 1;
$tabOrderbyWeek = array();

	$firstDate = $tabResults[0]['deliveredDate'];
	$lastDate = $tabResults[count($tabResults)-1]['deliveredDate'];

$numOrder = 0;
//On complétè le tableau tabOrderbyWeek pour affichage des colonnes en semaines sur les 6 prohains mois
foreach ($tabResults as $result) {


	$partNumberKey = $result['family'] . $result['countryCode'].$result['codePochette'].$result['partNumber'];
	$keyO = TrouveIndex($tabOrderbyWeek,$partNumberKey,'partNumberKey');
	$colorDefautHTML = "<td style='";
	$colorHTML = $colorDefautHTML;
	if($result['wip']=="DELIVERED")
	$colorHTML = "<td style='background-color:#61CE70;";
	else if($result['wip']=="IN PROGRESS")
	$colorHTML = "<td style='background-color:#E9F782;";
	else if($result['wip']=="BLOCKED")
	$colorHTML = "<td style='background-color:#FF735D;";

	if($result['quantity'] > 0)
	$colorHTML .= "font-weight:bold;'>";
	else
	$colorHTML .= "'>";

	$id_order = $result['id'];

	$good_format=strtotime ($result['deliveredDate']);
	$deliveredWeek = date('W',$good_format);
	if($keyO >=	0)
	{
		if(array_key_exists("w.".(0+$deliveredWeek)."<br>".substr($result['deliveredDate'],0,4),	$tabOrderbyWeek[$keyO]) !=false)
		{
			$tabOrderbyWeek[$keyO]["w.".(0+$deliveredWeek)."<br>".substr($result['deliveredDate'],0,4)] = $colorHTML.$result['quantity']."</td>";
			$tabOrderbyWeek[$keyO]['ids'] .= "|" . $result['id'];
		}

	}
	else {

		$tabOrderbyWeek[$numOrder]['family'] = "<td>".$result['family']."</td>";
		$tabOrderbyWeek[$numOrder]['countryCode'] = "<td>".$result['countryCode']."</td>";
		$tabOrderbyWeek[$numOrder]['countryName'] = "<td>".$result['countryName']."</td>";
		$tabOrderbyWeek[$numOrder]['codePochette'] = "<td>".$result['codePochette']."</td>";
		$tabOrderbyWeek[$numOrder]['partNumber'] = "<td>".$result['partNumber']."</td>";

		$strToday = strtotime ($firstDate);
		$strlastDay = strtotime ($lastDate);
		$todayWeek = date('W',$strToday);

		$diffDate =	dateDiff($strToday, $strlastDay);
		$annee = date('Y',$strToday);
		for($x=0;$x<($diffDate['day']/7);$x++)
		{
			if($x+intval($todayWeek) > 53)
			{
				$todayWeek = -$x+1;
				$annee++;
			}
			$tabOrderbyWeek[$numOrder]["w.".($x+intval($todayWeek))."<br>".$annee] = "<td>0</td>";
		}

		if(array_key_exists("w.".(0+$deliveredWeek)."<br>".substr($result['deliveredDate'],0,4),	$tabOrderbyWeek[$numOrder]) !=false)
		{
			$tabOrderbyWeek[$numOrder]["w.".(0+$deliveredWeek)."<br>".substr($result['deliveredDate'],0,4)] = $colorHTML.$result['quantity']."</td>";
		}
		$tabOrderbyWeek[$numOrder]['partNumberKey'] = $partNumberKey;
		$tabOrderbyWeek[$numOrder]['ids'] = $result['id'];





		$numOrder++;
	}


	$O++;
}



$data['tabOrderbyWeek'] = $tabOrderbyWeek;
//ON affiche les résultats
$bodyTable ="";
$data['orders'] = "<h3 style='color:black;text-align:center;'>%ORDER% find</h3>";
$x=0;
$headerTable = "<div style='margin-right:-10%;margin-left:-10%;padding:10px;background-color:#FFF;border:solid 1px;border-radius:5px;width:120%;overflow-x: auto;white-space: nowrap;'>";

$headerTable .= "<table id='order_table' class='order-table'><thead><tr>";
foreach ($tabOrderbyWeek as $keyO => $result) {
	$bodyTable .= "<tr id='row-".($x+1)."' onclick='affichePopup(\"".$result['ids']."\",\"row-".($x+1)."\")'>";
	$nbweek = 0;
	$col=0;
	foreach ($result as $key => $value) {
		if($x==0){
			if(stristr($key,"<br>") && strlen($key) > 5)
			{
				if($nbweek > 0 && intval(substr($key,2,2)) >1)
				$key = intval(substr($key,2,2));

				$nbweek++;
			}

			if($key == "family")
			$key = "Family";
			if($key == "countryCode")
			$key = "Country <br> Code";
			if($key == "countryName")
			$key = "Country Name";
			if($key == "codePochette")
			$key = "Code <br> Pochette";
			if($key == "partNumber")
			$key = "Part Number";

			if($col<count($result)-2)
			$headerTable .= "<th>".$key."</th>";
		}
		if($col<count($result)-2){
			$bodyTable .= $value;
		}
		$col++;
	}
	$bodyTable .= "</tr>";
	$x++;
}
$headerTable .= "</tr></thead><tbody>";

$data['orders'] .=$headerTable . $bodyTable . "</tbody></table></div>";


if($O > 1)
$data['orders'] = str_replace("%ORDER%",$O. " Orders",$data['orders']);
else {
	$data['orders'] = str_replace("%ORDER%",$O. " Order",$data['orders']);
}


//Légende
$data['orders'] .= "<br><br><h5>WIP Legend</h5>";
$data['orders'] .= "<table><tr>
<td style='background-color:#FFFFFF;'>PREFLIGHT</td>
<td style='background-color:#61CE70;'>IN PROGRESS</td>
<td style='background-color:#E9F782;'>BLOCKED</td>
<td style='background-color:#FF735D;'>DELIVERED</td><tr></table>";

if($result['wip']=="DELIVERED")
$colorHTML = "<td style='background-color:#61CE70;";
else if($result['wip']=="IN PROGRESS")
$colorHTML = "<td style='background-color:#E9F782;";
else if($result['wip']=="BLOCKED")
$colorHTML = "<td style='background-color:#FF735D;";

echo json_encode($data); // on renvoie en JSON les résultats





?>
