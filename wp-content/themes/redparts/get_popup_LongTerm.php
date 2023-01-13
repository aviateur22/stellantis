<?php


setlocale(LC_TIME, "fr_FR");

require('/home/mdwfrkglvc/www/wp-config.php');
global $wpdb;
global $tabColorStatut;

error_reporting(E_ALL);

$user_info = wp_get_current_user();
$par_qui = $user_info->first_name . " " . $user_info->last_name;

$ids = $_POST['ids'];
$id_order = 0;

$data ="";
if(isset($ids)){
	$sqlReq = str_replace("|"," OR id=",$ids) . ")";
	$_sql = "SELECT * FROM forecasts WHERE wip <> 'PREPARATION' AND "."(id=" . $sqlReq . " ORDER BY deliveredDate";
  $tabResultsOrder = $wpdb->get_results($_sql, ARRAY_A);
  if($wpdb->num_rows == "0"){
    echo "Error in database";
    return;
  }

	$data = "<div>";
	$data.= "<h5>Part Number : ".$tabResultsOrder[0]["partNumber"]."</h5>";
	$data.= "<p>Total quantity from ".$tabResultsOrder[0]["deliveredDate"]." to " .$tabResultsOrder[count($tabResultsOrder)-1]["deliveredDate"]." : <strong>%TOTAL%</strong></p>";
	$total = 0;
	$data .= "<table class='popup-table'><thead><tr><th>Week</th><th>Quantity</th><th>WIP</th><th>Details</th></tr></thead><tbody>";
	foreach ($tabResultsOrder as $key => $value) {
		$total += $value['quantity'];
		// if($value['wip']=="DELIVERED")
		// $colorHTML = "<td style='background-color:".$tabColorStatut['DELIVERED'].";'>";
		// else if($value['wip']=="IN PROGRESS")
		// $colorHTML = "<td style='background-color:".$tabColorStatut['IN PROGRESS'].";'>";
		// else if($value['wip']=="BLOCKED")
		// $colorHTML = "<td style='background-color:".$tabColorStatut['BLOCKED'].";'>";
		// else if($value['wip']=="PREFLIGT")
		// $colorHTML = "<td style='background-color:".$tabColorStatut['PREFLIGT'].";'>";
		// else if($value['wip']=="READY")
		// $colorHTML = "<td style='background-color:".$tabColorStatut['READY'].";'>";
		// else {
		// 	$colorHTML = "<td>";
		// }
		$data .= "<tr>";
		if($value['shortTerm'] == 1)
		{
			$date = new DateTime($value['deliveredDate']);
			$data .= "<td>".$date->format("d/m/Y")."</td>";

		}
		else
		$data .= "<td>w.".$value['week']." - ".$value['year']."</td>";

		$data .= "<td>".$value['quantity']."</td><td>Forecast</td>";

		if($value['shortTerm'] == 1)
		$data .= "<td>Short Term</td></tr>";
		else
		$data .= "<td>Long Term</td></tr>";


	}
	$data = str_replace("%TOTAL%",$total." prints",$data);
	$data .= "</body></table></div>";


}
else {
	echo "Error : order ids are not valid";
	return;
}

echo $data;





?>
