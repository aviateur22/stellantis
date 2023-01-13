<?php




require('/home/mdwfrkglvc/www/wp-config.php');

error_reporting(E_ALL);

/////////// UPLOAD FICHIER /////////////////

/* Get the name of the uploaded file */
$filename = $_FILES['file']['name'];

/* Choose where to save the uploaded file */
$location = "/home/mdwfrkglvc/www/wp-content/uploads/orders/".$filename;

/* Save the uploaded file to the local filesystem */
 if ( move_uploaded_file($_FILES['file']['tmp_name'], $location) ) {

 } else {
   echo 'Erreur : file not uploaded';
   return;
}





?>
