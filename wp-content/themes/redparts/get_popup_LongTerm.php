<?php



setlocale(LC_TIME, "fr_FR");

require('/home/mdwfrkglvc/www/wp-config.php');
global $wpdb;

error_reporting(E_ALL);

$user_info = wp_get_current_user();
$par_qui = $user_info->first_name . " " . $user_info->last_name;

$ids = $_POST['ids'];
$id_order = 0;

$data ="";
if(isset($ids)){
	$sqlReq = str_replace("|"," OR id=",$ids) . ")";
	$_sql = "SELECT * FROM orders WHERE wip <> 'PREPARATION' AND "."(id=" . $sqlReq;
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
		if($value['wip']=="DELIVERED")
		$colorHTML = "<td style='background-color:#61CE70;'>";
		else if($value['wip']=="IN PROGRESS")
		$colorHTML = "<td style='background-color:#E9F782;'>";
		else if($value['wip']=="BLOCKED")
		$colorHTML = "<td style='background-color:#FF735D;'>";
		else {
			$colorHTML = "<td>";
		}
		$data .= "<tr>
		<td>w.".$value['week']." - ".$value['year']."</td>
		<td>".$value['quantity']."</td>"
		.$colorHTML.$value['wip']."</td>
		<td></td>
		</tr>";

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
