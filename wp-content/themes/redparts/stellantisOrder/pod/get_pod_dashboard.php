<?php

setlocale(LC_TIME, "fr_FR");

require('/home/mdwfrkglvc/www/wp-config.php');
global $wpdb;

error_reporting(E_ALL);


//Variable globale du fichier function.php
global $tabColorStatut;

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



function get_pod_order_dashboard()
{
  setlocale(LC_TIME, "fr_FR");

  require('/home/mdwfrkglvc/www/wp-config.php');
  global $wpdb;

  error_reporting(E_ALL);


  //Variable globale du fichier function.php
  global $tabColorStatut;

  $user = wp_get_current_user();
  $par_qui = $user->first_name . " " . $user->last_name;
  $userid=$user->ID;
  $user_info = get_userdata($userid);
  $roles = implode(", ",$user_info->roles);



  //récupération des variables du FRONT
  $type = $_POST['type'];
  $dateDebut = $_POST['startDate'];

  if (isset($dateDebut) == false)
  $dateDebut = date("Y-m-d");

  $strdateDebut = strtotime ($dateDebut);

  $data = array();
  $tabResults = array();

  if(isset($dateDebut)) {


    $_sql = "SELECT * FROM orders WHERE isPOD = 1 AND deliveredDate >= '".$dateDebut."'";
    $_sql.= " ORDER BY orderBuyer ASC, deliveredDate ASC";

    //Execution de la reqûete et complétion du tableau tabResults
    $tabResults = $wpdb->get_results($_sql, ARRAY_A);
    //Si pas de donnée, on renvoi un message
    if($wpdb->num_rows == '0'){
      $data['orders'] = "<h3 style='color:red;text-align:center;'>No order find</h3>";
      echo json_encode($data); // on renvoie en JSON les résultats
      return;
    }
  }
  else {
    echo "Erreur : Données d'entrée non reçues";
    return;
  }


  $O = 0;
  $tabOrderbyWeek = array();

  $lastDate = $tabResults[count($tabResults)-1]['deliveredDate'];
  $strlastDay = strtotime ($lastDate);

  $numOrder = 0;
  //On complétè le tableau tabOrderbyWeek pour affichage des colonnes en semaines sur les 6 prohains mois
  foreach ($tabResults as $result) {


    $productNameKey = $result['productName'];
    $keyO = TrouveIndex($tabOrderbyWeek,$productNameKey,'productNameKey');
    $colorDefautHTML = "<td style='";
    $colorHTML = $colorDefautHTML;


    if($result['quantity'] > 0)
    $colorHTML .= "font-weight:bold;'>";
    else
    $colorHTML .= "'>";

    $id_order = $result['id'];

    if($keyO >=	0)
    {
      if(array_key_exists($result['deliveredDate'],	$tabOrderbyWeek[$keyO]) !=false)
      {
        //Mise à jour

          if(intval(strip_tags($tabOrderbyWeek[$keyO][$result['deliveredDate']])) > 0)
          $result['quantity'] += intval(strip_tags($tabOrderbyWeek[$keyO][$result['deliveredDate']]));

          $tabOrderbyWeek[$keyO][$result['deliveredDate']] = $colorHTML."<span style='padding:5px;border:solid 1px;'>".$result['quantity']."</span></td>";


        $tabOrderbyWeek[$keyO]['ids'] .= "|" . $result['id'];
      }
    }
    else {


      $tabOrderbyWeek[$numOrder]['productName'] = "<td>".$result['productName']."</td>";

      $diffDate =	dateDiff($strdateDebut, $strlastDay);
      $dateNew = $dateDebut;
      for($x=0;$x<$diffDate['day']+1;$x++)
      {
        $dateNew = date('Y-m-d', strtotime($dateNew. ' + 1 days'));
        $tabOrderbyWeek[$numOrder][$dateNew] = "<td>0</td>";
      }

      if(array_key_exists($result['deliveredDate'],	$tabOrderbyWeek[$numOrder]) !=false)
      {
        //Nouvelle entrée

       $tabOrderbyWeek[$numOrder][$result['deliveredDate']] = $colorHTML.$result['quantity']."</td>";

       $tabOrderbyWeek[$numOrder]['productNameKey'] = $result['productName'];
       $tabOrderbyWeek[$numOrder]['ids'] = $result['id'];
       $tabOrderbyWeek[$numOrder]['orderBuyer'] = $result['orderBuyer'];
       $tabOrderbyWeek[$numOrder]['model'] = $result['model'];



      $numOrder++;
    }
  }
     $O++;
   }



  //ON affiche les résultats
  $temp_orderBuyer_model = "";
  $colorModel = "grey";
  $bodyTable ="";
  $data['orders'] = "<h3 style='margin-top:10px;color:black;text-align:center;'>%ORDER% find</h3>";
  $x=0;
  $headerTable = "<div style='margin-bottom:150px;' class='order-div'>";

  $headerTable .= "<table id='order_table' class='order-table'><thead><tr>";
  foreach ($tabOrderbyWeek as $keyO => $result) {
    if($temp_orderBuyer_model != $result['orderBuyer']. " - " .$result['model'])
    {
      if($colorModel=="grey")
      {
        $colorModel="#CDCD00";
      }
      else {
        $colorModel="grey";
      }
      $temp_orderBuyer_model=$result['orderBuyer'] . " - " .$result['model'];
      $bodyTable .= "<tr style='background-color:".$colorModel.";color:white'><td style='text-align:left;font-weight:bold;'>".$temp_orderBuyer_model."</td></tr>";
    }


    $bodyTable .= "<tr style='border-left:solid 5px;border-color:".$colorModel.";' id='row-".($x+1)."' onclick='affichePopup(\"".$result['ids']."\",\"row-".($x+1)."\")'>";
    $nbweek = 0;
    $col=0;
    foreach ($result as $key => $value) {
      if($x==0){


        // if($key == "family")
        // $key = "Family";
        // if($key == "countryCode")
        // $key = "Country <br> Code";
        // if($key == "countryName")
        // $key = "Country Name";
        // if($key == "codePochette")
        // $key = "Code <br> Pochette";
        // if($key == "partNumber")
        // $key = "Part Number";

        if($col<count($result)-4)
        $headerTable .= "<th>".$key."</th>";
      }
      if($col<count($result)-4){
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
  $data['orders'] = str_replace("%ORDER%",$O. " POD",$data['orders']);
  else {
    $data['orders'] = str_replace("%ORDER%",$O. " POD",$data['orders']);
  }


  //Légende
  // $data['orders'] .= "<br><br><h5>WIP Legend</h5>";
  // $data['orders'] .= "<p><span style='padding:5px;border:solid 1px;'>Short Term Forecasts</span>
  // <br><br>Long Term Forecasts</p>";

  return($data['orders']);

}



?>
