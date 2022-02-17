<?php
session_start();
set_time_limit (240); 
//Todo:Sicherheit: nur eingeloggte user durfen diese file aufrufen ... 


include('includes/dbconnect.inc.php'); 
include('includes/db_functions.inc.php'); 

$gebrochene_noten=array("1+","1","1-","2+","2","2-","3+","3","3-","4+","4","4-","5+","5","5-","6");
$ganze_noten=array("1","2","3","4","5","6");
	



function outputCSV($data) 
{
	$outputBuffer = fopen("php://output", 'w');

	//Überschriften erzeugen - unabhängig von der Inizierung des Arrays: Nimm die Keys des ersten Array als Überschrift.
	$first_value = reset($data);
	$keys= array_keys($first_value);
	fputcsv($outputBuffer, $keys);
	
	//Inhalte des cvs ausgeben.
	foreach($data as $val) 
	{
		fputcsv($outputBuffer, $val);
	}
	
        fclose($outputBuffer);

}
//Daten einlesen
$output = $_SESSION['liste'];
//Format festlegen
$outputformat = 'csv';


// Get-Anweisung auswerten:
if($_GET['download'] == 'lehrerliste')
{
	$filename = 'lehrerliste_'.date('d.m.Y');
}
elseif($_GET['download'] == 'schuelerliste')
{
	$filename = 'Schuelerliste_' . $_GET['lerngruppe'] .'_'. date('d.m.Y') ;
}
elseif($_GET['download'] == 'notenliste_csv')
{
	// Zensuren und Teinoten aus der DB holen und Variablen aktualisieren: 
	$_SESSION['zensurenliste'] = update_zensurenliste($database,$_SESSION['zensurenliste']);
	$_SESSION['notenarray'] = get_notenarray($database,$_SESSION['zensurenliste']);	
	
	// Überschrift: 
	$output[0][$_GET['lerngruppe']] = '';
	$output[0][$_GET['notenart']] = '';
	$i=3;
	//statistikarray vorbereiten:
	foreach ($gebrochene_noten as $note)
	{
		$statistikarray[$note]=0;
	}
	//Daten aus den Session-arrays zusamensetzen
	foreach ($_SESSION['liste'] as $zensur)
	{
			$output[$i][0] = $_SESSION['zensurenliste'][$zensur]['Name'];
			$output[$i][1] = $_SESSION['zensurenliste'][$zensur]['Vorname'];
			$output[$i][2] = $_SESSION['notenarray'][$zensur][$_GET['notenart']];
			
			//statistikarray befüllen: 
			$statistikarray[$output[$i][2]]++;			
			$i++;
	}
	//2 Freie Zeilen:
	$i++;
	$output[$i][0] = '';
	$i++;
	$output[$i][0] = '';
	$i++;
	
	//in aktueller Zeile 3 leere Spalten einrücken.
	$output[$i][0] = '';	
	$output[$i][1] = '';	
	$output[$i][2] = '';	
	$j=3;
	//Statistikarray:
	
	foreach ($gebrochene_noten as $note)
	{	
		$output[$i][$j]=$note;
		$j++;	
	}
	$output[$i][$j]='NT';
	$i++;
	//in aktueller Zeile 3 leere Spalten einrücken.
	$output[$i][0] = '';	
	$output[$i][1] = '';	
	$output[$i][2] = '';	
	$j=3;
	foreach ($statistikarray as $note)
	{	
		$output[$i][$j]=$note;
		$j++;	
	}


	//4 Freie Zeilen:
	$i++;
	$output[$i][0] = '';
	$i++;
	$output[$i][0] = '';
	$i++;
	$output[$i][0] = '';
	$i++;
	$output[$i][0] = '';

	//Unterschrift:
	$output[$i][0] = 'MG, ' . date('d.m.Y');	
	$output[$i][1] = '______________';
	$i++;
	$output[$i][0] = '';
	$output[$i][1] = 'Unterschrift';
	
	//filename festlegen	
	$filename = 'Noten_' . $_GET['notenart'] . '_' . $_GET['lerngruppe'] . '_' . date('d.m.Y');
	$outputformat = 'csv';
}
else
{
	echo 'nothing to do ... ';
	die;
}

if($outputformat == 'csv')
{
	//output abschicken: 
	header('Content-Encoding: UTF-8');
	header("content-type:application/csv;charset=UTF-8");
	header("Content-Disposition: attachment; filename={$filename}.csv");
	header("Pragma: no-cache");
	header("Expires: 0");

	outputCSV($output);
} 
?>
