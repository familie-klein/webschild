<?php 
/********************************************
/ php-skript:
/ Database von Schild-mysql-datenbank öffnen 
********************************************/

//php classen zur mysql-db importieren
require_once('./includes/database.class.php');



//Daten abholen aus dem db.dat file
//Variablen:  $db_host, $db_user, $db_pass , $db_name 
$dbdaten = file_exists ("./includes/mysql_data.inc.php");

if($dbdaten){
	require_once("./includes/mysql_data.inc.php");	
}
else{
	echo '<a href="./setup/index.php">Bitte richten sie den Zugang zur Datenbank ein.</a>';
	die;
}

$database = new Database();

// The database connection:
try {
    	$database->connect('mysql:host='.$db_host.';dbname='.$db_name.'', $db_user, $db_pass);
	} 
catch (DatabaseException $e) {
	echo "Keine Verbindung zur DB möglch. Bitte prüfen Sie die Netzwerkverbindung und stellen sie sicher, dass der Mysql-Server erreichbar ist.";               	     
	}
?>
