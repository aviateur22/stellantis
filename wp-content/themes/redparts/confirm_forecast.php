<?php


setlocale(LC_TIME, "fr_FR");

require('/home/mdwfrkglvc/www/wp-config.php');
global $wpdb;

error_reporting(E_ALL);

$id_order = $_POST['id_order'];
$sidOldOrder = $_POST['sidOldOrder'];
$tabidOldOrder = explode(",",$sidOldOrder);
$_sql = "SELECT * FROM forecasts WHERE orderID = $id_order";
$tabResultsorder = $wpdb->get_results($_sql, ARRAY_A);
if($wpdb->num_rows == "0"){ //Cette commande n'existe pas, renvoi faux
echo "Error : Forecast not exist";
return;
}

if(isset($id_order) && $id_order > 0){

    $data_update  = array(
    'wip' => "PREFLIGT");
    $data_where = array('orderID'=>$id_order);
    $wpdb->update('forecasts', $data_update, $data_where);

    //On supprime les anciennes orders qui ont été mis à jour pour éviter les doublons
    foreach ($tabidOldOrder as $key => $value) {
      $wpdb->delete('forecasts',array('id' => intval($value)));

    }


//On supprimre les order en préparation si la date est inférieur à celle d'aujourdhui
$datedujour = date("Y-m-d") . " 00:00:00";
$wpdb->query($wpdb->prepare("DELETE FROM forecasts WHERE wip = 'PREPARATION' AND orderDate < '".$datedujour."'"));



}
else {
  echo "Error : order ID is not valid";
  return;
}

echo "OK";





?>
