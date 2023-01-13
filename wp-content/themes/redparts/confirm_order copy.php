<?php

function CreateXML($tabResultsorder)
{
  $xmlFile = new DOMDocument('1.0', 'utf-8');
      $xmlFile->appendChild($bibliotheque = $xmlFile->createElement('bibliotheque'));

    foreach ($tabResultsorder as $key => $value) {

          $bibliotheque->appendChild($livre = $xmlFile->createElement('order'));
          $livre->appendChild($xmlFile->createElement('oderId', $value['id']));
          $livre->appendChild($xmlFile->createElement('orderDate', $value['orderDate']));
          $livre->appendChild($xmlFile->createElement('orderFrom', $value['orderFrom']));
          $livre->appendChild($xmlFile->createElement('orderBuyer', $value['orderBuyer']));
          $livre->appendChild($xmlFile->createElement('countryName', $value['countryName']));
          $livre->appendChild($xmlFile->createElement('countryCode', $value['countryCode']));
          $livre->appendChild($xmlFile->createElement('partNumber', $value['partNumber']));
          $livre->appendChild($xmlFile->createElement('codePochette', $value['codePochette']));
          $livre->appendChild($xmlFile->createElement('quantity', $value['quantity']));
          $livre->appendChild($xmlFile->createElement('deliveredDate', $value['deliveredDate']));
          $livre->appendChild($xmlFile->createElement('wip', $value['wip']));
          $livre->appendChild($xmlFile->createElement('lien_couv', $value['lien_couv']));
          $livre->appendChild($xmlFile->createElement('family', $value['family']));
          $livre->appendChild($xmlFile->createElement('week', $value['week']));
          $livre->appendChild($xmlFile->createElement('weekYear', $value['weekYear']));

          $prevision = 0;
          //Analyse des prévisions
          $livre->appendChild($xmlFile->createElement('previsionsOn8Weeks', $prevision));
  }

      $xmlFile->formatOutput = true;
      $xmlFile->save('/home/mdwfrkglvc/www/wp-content/uploads/OF-STE'.$value['id'].'.xml');



}
setlocale(LC_TIME, "fr_FR");

require('/home/mdwfrkglvc/www/wp-config.php');
global $wpdb;

error_reporting(E_ALL);

$id_order = $_POST['id_order'];
$sidOldOrder = $_POST['sidOldOrder'];
$tabidOldOrder = explode(",",$sidOldOrder);
$_sql = "SELECT * FROM orders WHERE orderID = $id_order";
$tabResultsorder = $wpdb->get_results($_sql, ARRAY_A);
if($wpdb->num_rows == "0"){ //Cette commande n'existe pas, renvoi faux
echo "Error : order not exist";
return;
}

if(isset($id_order)){

    $data_update  = array(
    'wip' => "PREFLIGT");
    $data_where = array('orderID'=>$id_order);
    $wpdb->update('orders', $data_update, $data_where);

    //On supprime les anciennes orders qui ont été mis à jour pour éviter les doublons
    foreach ($tabidOldOrder as $key => $value) {
      $wpdb->delete('orders',array('id' => intval($value)));

    }

//CreateXML($tabResultsorder);


//On supprimre les order en préparation si la date est inférieur à celle d'aujourdhui
$datedujour = date("Y-m-d") . " 00:00:00";
$wpdb->query($wpdb->prepare("DELETE FROM orders WHERE wip = 'PREPARATION' AND orderDate < '".$datedujour."'"));



}
else {
  echo "Error : order ID is not valid";
  return;
}

echo "OK";





?>
