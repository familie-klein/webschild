<?php
session_start();
 
//Prüfen, ob ein Lehrer mit Lehrerkürzel eingeloogt ist.
if (isset($_SESSION['krz'])) {
  

require_once ('includes/dbconnect.inc.php');
require_once ('includes/functions.inc.php');
require_once ('includes/db_functions.inc.php');
require_once ('includes/config.inc.php');

//######################### Auswertung der Post Variablen #############################################




if ($_POST['mysql-table'])
{

	if (($_POST['mysql-table'] == 'schuelerleistungsdaten'))
	{	

		$i=0; 						//Anzahl der Einträge in der Zesurenliste
		$endnoteneintrag = 0;	//Anzahl der eingetragenen Noten
		$n_eintraege = 0; 		//Anzahl der fehlenden Einträge
		$fstdeintrag = 0; 		//Anzahl der eingetragenen Fehlstundeneinträge
		$ufstdeintrag = 0;		//Anzahl der eingetragenen unentshuldigten Fehlstundeneinträge
		$teilnoteneintrag = 0;  //Anzahl der Teilnoteneinträge


		$keys = array_keys ($_POST);
		foreach($_POST as $value)
		{
			//restliche POST variable verarbeiten:
			if(($value != 'schuelerleistungsdaten'))
			{
				$pointer = explode('-', $keys[$i]);
				
				$id = $pointer[0];
				$varname = $pointer[1];
				if ($varname == 'Jahrgang')
				{
					$jahrgang = $value;
				}
				else
				{			
					//Groß und Kleinschreibung bei den Noteneinträgen bereinigen -> alles auf Großschreibung. 
					$value = strtoupper ($value);

					//echo $varname . check_input($varname,$value,$jahrgang) . '<br>';
					//echo $varname . ' '.  $jahrgang . ' '. $value .'<br>'; 
					if (check_input($varname,$value,$jahrgang))
					{	
						$teilnoten = array ('KA_1','KA_2','KA_3', 'Somi_1','Somi_2','Somi_Ges','Klausur_1', 'Klausur_2', 'Schr_Ges');
	
						//echo $varname . ' '.  $jahrgang . ' '. $value .'<br>'; 
						if (($varname == 'Endnote') )
						{		
						
							//Sql Befehl wird zusammengesetzt: 
							$sql = "UPDATE `SchuelerLeistungsdaten` SET `NotenKrz` = '". $value. "' WHERE `SchuelerLeistungsdaten`.`ID` = ". $id .";";		
							//in die Datenbank schreiben:		
							$result = $database -> query($sql);
							//counter für die Rückmeldung:
							$endnoteneintrag++;
						}
						elseif ($varname == 'Fehlstd')
						{
							//Sql Befehl wird zusammengesetzt: 
							$sql = "UPDATE `SchuelerLeistungsdaten` SET `Fehlstd` = '". $value. "' WHERE `SchuelerLeistungsdaten`.`ID` = ". $id .";";		
							//in die Datenbank schreiben:		
							$result = $database -> query($sql);
							//counter für die Rückmeldung:
							$fstdeintrag++;
						}
						elseif ($varname == 'uFehlstd')
						{
							//Sql Befehl wird zusammengesetzt: 
							$sql = "UPDATE `SchuelerLeistungsdaten` SET `uFehlstd` = '". $value. "' WHERE `SchuelerLeistungsdaten`.`ID` = ". $id .";";		
							//in die Datenbank schreiben:		
							$result = $database -> query($sql);
							//counter für die Rückmeldung:
							$ufstdeintrag++;
						}
						elseif(in_array($varname,$teilnoten))	
						{
							//echo $varname . '<p>';
							if($value)
							{		
								//TODO:							
								//nur speichern, wenn änderungen durchgeführt wurden. 
								// auskommentiert, weil Schild NRW die Funktion mittlerweile anders bedient
								//$sql = "INSERT INTO `webschild_teilnoten` (`id`, `noten_id`, `timecode`, `note`, `notentyp`, `lehrer`) VALUES (NULL, '". $id ."', CURRENT_TIMESTAMP, '". $value. "', '". $varname ."','". $_SESSION['krz'] ."');;";		
								//in die Datenbank schreiben:		
								//$result = $database -> query($sql);
								$teilnoteneintrag++;
							}
						}
						
					}
					else
					{
							$n_eintraege++;		
					}

				}
			}
			$i++;
		}		
		
		// Aufbereitung der Rückmeldung:
		if ($endnoteneintrag)
		{		
			echo ''. $endnoteneintrag .' Endnoten sind gespeichert! ';
		}
		if ($fstdeintrag)
		{
			echo 'Fehlstunden gespeichert! ';
		}
		if ($ufstdeintrag)
		{
			echo 'Unentschuldigte Fehlstunden gespeichert! ';
		}
		if ($teilnoteneintrag)
		{
			echo ''. $teilnoteneintrag .' Teilnoten sind abgespeichert! 	';
		}
		if ($n_eintraege)
		{
		//	echo $n_eintraege .' Noten- bzw. Fehlstundeneinträge wurden nicht angegeben bzw. hatten das falsche Format.<br>
			//		';
		}
		
	}
	else 
	{
		echo ' 
		Ihre Eingabe kann nicht verarbeitet werden!
		<p><a href=http://'. $_SERVER['SERVER_NAME'] .'/>weiter</a>
		';
	}

}
else 
{
		echo ' 
		Keine Daten zur Verarbeitung empfangen.
		<p><a href=http://'. $_SERVER['SERVER_NAME'] .'/>weiter</a>
		';
}




} else {
   echo "Bitte erst <a href=http://". $_SERVER['SERVER_NAME'] ."/>hier</a> einloggen!";
}

?>
