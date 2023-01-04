<?php


require('/home/mdwfrkglvc/www/wp-config.php');
global $wpdb;

error_reporting(E_ALL);

$type = $_POST['type'];

$data = array('id_order'=>0,
'intro'=>'');


if(isset($type))
{

  $user_info = wp_get_current_user();
  $par_qui = $user_info->first_name . " " . $user_info->last_name;
  $data['intro'] = "<p>Hello <strong>". $par_qui . "</strong> (you are not <strong>".$par_qui."</strong> ? ";
  $data['intro'] .= '<a href="https://mdw-05.fr/reseau-psa/customer-logout/?_wpnonce=c4b5de2738">Logout</a>)</p>';
if($par_qui == "Clément Thuaudet")
{
    $data['intro'] .='<button onclick="gestionAppel(\''.'order_model.xlsx'.'\')">TEST</button>';
}

}
else{
  echo "Error ";
  return;
}


echo json_encode($data); // on renvoie en JSON les résultats





?>
