<?php
setlocale(LC_TIME, "fr_FR");
require_once('./stellantisOrder/services/MySqlOrderRepository.php');
require_once('./stellantisOrder/helpers/DashboardOrderHelper.php');
require_once('./stellantisOrder/html/DisplayDashboardOrder.php');

require('/home/mdwfrkglvc/www/wp-config.php');
global $wpdb;
error_reporting(E_ALL);
global $tabColorStatut;


// Initilisation
$orderRepository = new MySqlOrderRepository();
$dashboardHelper = new DashboardHelper($orderRepository);

$test = $dashboardHelper->setDashboardOrders(null, '2023-02-25');

// Récupération des données
$dashboardOrders = $dashboardHelper->getDashboardOrders();
$intervalDays = $dashboardHelper->getIntervalDays();



// Creation html
$displayDashboardOrder = new DisplayDashboardOrder($dashboardOrders, $intervalDays);


$data['orders'] = $displayDashboardOrder->createHtml();


echo(json_encode($data));



// function dateDiff($date1, $date2){
//   $diff = abs($date1 - $date2); // abs pour avoir la valeur absolute, ainsi éviter d'avoir une différence négative
//   $retour = array();

//   $tmp = $diff;
//   $retour['second'] = $tmp % 60;

//   $tmp = floor( ($tmp - $retour['second']) /60 );
//   $retour['minute'] = $tmp % 60;

//   $tmp = floor( ($tmp - $retour['minute'])/60 );
//   $retour['hour'] = $tmp % 24;

//   $tmp = floor( ($tmp - $retour['hour'])  /24 );
//   $retour['day'] = $tmp;

//   return $retour;
// }

// function TrouveIndex($tabarray,$searchValue,$colone)
// {
//   $numF = 0;
//   $keyF = -1;
//   foreach ($tabarray as $value) {
//     if($value[$colone] == $searchValue)
//     {
//       $keyF = $numF;
//     }
//     $numF++;
//   }
//   return $keyF;
// }



// $user_info = wp_get_current_user();
// $par_qui = $user_info->first_name . " " . $user_info->last_name;

// $todayYear = date("Y");
// $strToday = strtotime (date("Y-m-d"));
// $todayWeek = date('W',$strToday);

// $type = $_POST['type'];
// $dateDebut = date("Y-m-d");
// $dateFin = $_POST['dateFin'];



// $data = array();
// $tabResults = array();
// if(isset($type) && isset($dateDebut) && isset($dateFin))
// {
//   $_sql = "SELECT * FROM orders WHERE wip <> 'PREPARATION' AND deliveredDate >= '".$dateDebut."' AND deliveredDate <= '".$dateFin."' ORDER BY deliveredDate";


//   $tabResults = $wpdb->get_results($_sql, ARRAY_A);
//   if($wpdb->num_rows == "0"){
//     $data['orders'] = "<h3 style='color:red;text-align:center;'>No order find</h3>";
//     echo json_encode($data); // on renvoie en JSON les résultats
//     return;
//   }
// }
// else {
//   echo "Erreur : Données d'entrée non reçues";
//   return;
// }
// $O = 1;
// $tabOrderbyWeek = array();

// $lastDate = $tabResults[count($tabResults)-1]['deliveredDate'];

// $numOrder = 0;
// //On complétè le tableau tabOrderbyWeek pour affichage des colonnes en semaines sur les 6 prohains mois
// foreach ($tabResults as $result) {
//   $anneeOrder = substr($result['deliveredDate'],0,4);

//   $partNumberKey = $result['family'] . $result['countryCode'].$result['codePochette'].$result['partNumber'];
//   $keyO = TrouveIndex($tabOrderbyWeek,$partNumberKey,'partNumberKey');
//   $colorDefautHTML = "<td style='";
//   $colorHTML = $colorDefautHTML;
//   if($result['wip']=="DELIVERED")
//   $colorHTML = "<td style='background-color:".$tabColorStatut['DELIVERED'].";";
//   else if($result['wip']=="IN PROGRESS")
//   $colorHTML = "<td style='background-color:".$tabColorStatut['IN PROGRESS'].";";
//   else if($result['wip']=="BLOCKED")
//   $colorHTML = "<td style='background-color:".$tabColorStatut['BLOCKED'].";";
//   else if($result['wip']=="READY")
//   $colorHTML = "<td style='background-color:".$tabColorStatut['READY'].";";

//   if($result['quantity'] > 0)
//   $colorHTML .= "font-weight:bold;'>";
//   else
//   $colorHTML .= "'>";

//   $id_order = $result['id'];

//   $good_format=strtotime ($result['deliveredDate']);
//   $deliveredWeek = date('W',$good_format);
//   if($keyO >=	0)
//   {
//     if(array_key_exists("w.".(0+$deliveredWeek)."<br>".$anneeOrder,	$tabOrderbyWeek[$keyO]) !=false)
//     {
//       //Mise à jour
//       if($result['shortTerm']==1 && ($deliveredWeek <= $todayWeek+2 && $anneeOrder == $todayYear ) || ($todayWeek >=52 && $deliveredWeek < 2 && $anneeOrder-1 == $todayYear))
//       {
//         if(intval(strip_tags($tabOrderbyWeek[$keyO]["w.".(0+$deliveredWeek)."<br>".$anneeOrder])) > 0)
//         $result['quantity'] += intval(strip_tags($tabOrderbyWeek[$keyO]["w.".(0+$deliveredWeek)."<br>".$anneeOrder]));

//         $tabOrderbyWeek[$keyO]["w.".(0+$deliveredWeek)."<br>".$anneeOrder] = $colorHTML."<span style='padding:5px;border:solid 1px;'>".$result['quantity']."</span></td>";

//       }
//       else if($result['shortTerm']==0){
//         $tabOrderbyWeek[$keyO]["w.".(0+$deliveredWeek)."<br>".$anneeOrder] = $colorHTML.$result['quantity']."</td>";
//       }
//       $tabOrderbyWeek[$keyO]['ids'] .= "|" . $result['id'];
//     }

//   }
//   else {

//     $tabOrderbyWeek[$numOrder]['family'] = "<td>".$result['family']."</td>";
//     $tabOrderbyWeek[$numOrder]['countryCode'] = "<td>".$result['countryCode']."</td>";
//     $tabOrderbyWeek[$numOrder]['countryName'] = "<td>".$result['countryName']."</td>";
//     $tabOrderbyWeek[$numOrder]['codePochette'] = "<td>".$result['codePochette']."</td>";
//     $tabOrderbyWeek[$numOrder]['partNumber'] = "<td>".$result['partNumber']."</td>";

//     $firstWeek = $todayWeek;
//     $strlastDay = strtotime ($dateFin);
//     $diffDate =	dateDiff($strToday, $strlastDay);
//     $annee = date('Y',$strToday);

//     for($x=0;$x<($diffDate['day']/7);$x++)
//     {
//       if($x+intval($firstWeek) > 53)
//       {
//         $firstWeek = -$x+1;
//         $annee++;
//       }
//       $tabOrderbyWeek[$numOrder]["w.".($x+intval($firstWeek))."<br>".$annee] = "<td>0</td>";
//     }

//     if(array_key_exists("w.".(0+$deliveredWeek)."<br>".$anneeOrder,	$tabOrderbyWeek[$numOrder]) !=false)
//     {
//       //Nouvelle entrée
//       if($result['shortTerm']==1){
//         $tabOrderbyWeek[$numOrder]["w.".(0+$deliveredWeek)."<br>".$anneeOrder] = $colorHTML."<span style='padding:5px;border:solid 1px'>".$result['quantity']."</span></td>";
//       }
//       else {
//         $tabOrderbyWeek[$numOrder]["w.".(0+$deliveredWeek)."<br>".$anneeOrder] = $colorHTML.$result['quantity']."</td>";
//       }
//     }
//     $tabOrderbyWeek[$numOrder]['partNumberKey'] = $partNumberKey;
//     $tabOrderbyWeek[$numOrder]['ids'] = $result['id'];



//     $numOrder++;
//   }


//   $O++;
// }



// $data['tabOrderbyWeek'] = $tabOrderbyWeek;
// //ON affiche les résultats
// $bodyTable ="";
// $data['orders'] = "<h3 style='color:black;text-align:center;'>%ORDER% find</h3>";
// $x=0;
// $headerTable = "<div style='margin-right:-10%;margin-left:-10%;padding:10px;background-color:#FFF;border:solid 1px;border-radius:5px;width:120%;overflow-x: auto;white-space: nowrap;'>";

// $headerTable .= "<table id='order_table' class='order-table'><thead><tr>";
// foreach ($tabOrderbyWeek as $keyO => $result) {
//   $bodyTable .= "<tr id='row-".($x+1)."' onclick='affichePopup(\"".$result['ids']."\",\"row-".($x+1)."\")'>";
//   $nbweek = 0;
//   $col=0;
//   foreach ($result as $key => $value) {
//     if($x==0){
//       if(stristr($key,"<br>") && strlen($key) > 5)
//       {
//         if($nbweek > 0 && intval(substr($key,2,2)) >1)
//         $key = intval(substr($key,2,2));

//         $nbweek++;
//       }

//       if($key == "family")
//       $key = "Family";
//       if($key == "countryCode")
//       $key = "Country <br> Code";
//       if($key == "countryName")
//       $key = "Country Name";
//       if($key == "codePochette")
//       $key = "Code <br> Pochette";
//       if($key == "partNumber")
//       $key = "Part Number";

//       if($col<count($result)-2)
//       $headerTable .= "<th>".$key."</th>";
//     }
//     if($col<count($result)-2){
//       $bodyTable .= $value;
//     }
//     $col++;
//   }
//   $bodyTable .= "</tr>";
//   $x++;
// }
// $headerTable .= "</tr></thead><tbody>";

// $data['orders'] .=$headerTable . $bodyTable . "</tbody></table></div>";


// if($O > 1)
// $data['orders'] = str_replace("%ORDER%",$O. " Orders",$data['orders']);
// else {
//   $data['orders'] = str_replace("%ORDER%",$O. " Order",$data['orders']);
// }


// //Légende
// $data['orders'] .= "<br><br><h5>WIP Legend</h5>";
// $data['orders'] .= "<table><tr>
// <td style='background-color:".$tabColorStatut['PREFLIGT'].";'>PREFLIGHT</td>
// <td style='background-color:".$tabColorStatut['IN PROGRESS'].";'>IN PROGRESS</td>
// <td style='background-color:".$tabColorStatut['BLOCKED'].";'>BLOCKED</td>
// <td style='background-color:".$tabColorStatut['READY'].";'>READY</td>
// <td style='background-color:".$tabColorStatut['DELIVERED'].";'>DELIVERED</td><tr></table>";

// echo json_encode($data); // on renvoie en JSON les résultats





?>
