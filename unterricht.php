<?php 

include('includes/header.inc.php'); 

//####################################################################################################

// ist die Noteneingabe in der Schild DB gesperrt? -> die variable $eingabe wird entsprechend gesetzt
$eingabe = get_schild_noteneinstellungen($database);

// ist die Noteneingabe einzelner Jahrgangstufen über WebSchild gesperrt?
$noteneinstellungen = get_noteneinstellungen($database);

if(($eingabe == true))
{
	//####################################################################################################
	//Zensurenliste zusammenstellen
	if ($_SESSION['zensurenliste'])
	{
		$_SESSION['zensurenliste'] = update_zensurenliste($database,$_SESSION['zensurenliste']);
		// nur Noten und Fehlstunden aus performance Gründen aktualisieren:
		$result = $database->query("SELECT * FROM schuelerleistungsdaten WHERE FachLehrer='". $lehrer->Kuerzel ."';");	
		while ($obj = $database->fetchObject ($result) ) 
		{
				$_SESSION['zensurenliste'][$obj->ID][NotenKrz] = $obj->NotenKrz;
				$_SESSION['zensurenliste'][$obj->ID][Fehlstd] = $obj->Fehlstd;
				$_SESSION['zensurenliste'][$obj->ID][uFehlstd] = $obj->uFehlstd;
		}
	}
	else
	{
		//zensurelnliste erstellen und als Sessionvariable speichern
		$_SESSION['zensurenliste'] = zensurenliste_erstellen($database,$_SESSION['krz'],$_SESSION['schuljahr'],$_SESSION['abschnitt']);
	
	}


	//########### Klassen und Kurslisten zusammensetzen #####################################################
	// hier wird die ganze Zensurenliste des Lehrers einsortiert in ein Kurs und ein Klassenunterrichtsarray
	foreach($_SESSION['zensurenliste'] as $item)
	{
		if($item['Kurs_ID'])	
		{
			//kursliste wird erstellt:
			$kurs_IDs[$item['Kurs_ID']]['Kurs_ID']=$item['Kurs_ID'];
			$kursliste[$item['Kurs_ID']]['kurs_ID']=$item['Kurs_ID'];
			$kursliste[$item['Kurs_ID']]['kursbez']=$item['Kurs_Bez']; 
			$kursliste[$item['Kurs_ID']]['kursart']= $item['KursartAllg'];
		}
		else
		{	
			$pointer =  $item['Klasse'] . '-'. $item['Fach_ID'];
			$unterricht= $item['Klasse'] . '-' . $_SESSION['faecher_zuordnung'][$item['Fach_ID']];
			$klassenunterrichtliste[$pointer]['ID'] = $pointer ;
			$klassenunterrichtliste[$pointer]['klassenunterricht']=$unterricht ;  
			$klassenunterrichtliste[$pointer]['fach']=$_SESSION['faecher_zuordnung'][$item['Fach_ID']] ; 
			$klassenunterrichtliste[$pointer]['klasse']=$item['Klasse'] ; 
			$klassenunterrichtliste[$pointer]['unterrichtsart']=$item['KursartAllg'] ; 
		}
	}


	//Zuordnung zu den Jahrgängen in Kursliste setzen, um eindeutige Bezeichnungen zu in der Linkliste zu erhalten
	// linkliste zusammenstellen:
	foreach($kurs_IDs as $kursID)
	{
		
		# mysql Abfrage der Kurse im aktiven Schulhalbjahr
		//Daten aus der Mysql-db holen
		try 
		{
			$result = $database->query("SELECT * FROM kurse WHERE ID LIKE '".$kursID['Kurs_ID']."';");
		}
		catch (DatabaseException $e) 
		{
			echo ("Keine Datenbankabfrage möglich.");
		}
	
		while ($obj = $database->fetchObject ($result) ) 
		{	
			if ($obj->ASDJahrgang)
			{
				
				$kursliste[$kursID['Kurs_ID']]['jahrgang']= $obj->ASDJahrgang;
				$jahrgangOhneNull = $outputstring = str_replace("0", "", $obj->ASDJahrgang);
				$kursliste[$kursID['Kurs_ID']]['langbezeichnung']= $jahrgangOhneNull . '_' .$kursliste[$kursID['Kurs_ID']]['kursbez'] ;
			}
			else
			{
				$kursliste[$kursID['Kurs_ID']]['langbezeichnung']= 'AG_' . $kursliste[$kursID['Kurs_ID']]['kursbez'];
			}
						
		}	
	}
	//Klassen und Kurs-listen in Sesion speichern
	$_SESSION['klassenunterrichtliste']=$klassenunterrichtliste;
	$_SESSION['kursunterrichtliste']=$kursliste;
	//echo '<pre>';
	//print_r($_SESSION['klassenunterrichtliste']);
	//echo '</pre>';

	

	//####################################################################################################
	//################## Tabelle mit zwei links zusammenbasteln ########################################
	
	

	$table = '
		<table id="myTable" class="tablesorter table table-hover">
			
			';

	$id_list= array_keys($klassenunterrichtliste);
	$first_value = reset($klassenunterrichtliste);
	$keys= array_keys($first_value);
	$i=0;
	
	
	$id_list= array_keys($klassenunterrichtliste);
	foreach($id_list as $ID)
	{
			$linklist[$klassenunterrichtliste[$ID]['klassenunterricht']] [bezeichnung] = $klassenunterrichtliste[$ID]['klassenunterricht'];
			$linklist[$klassenunterrichtliste[$ID]['klassenunterricht']] [schuelerlistenlink] =  '
							<a href="klassen.php?'.'klasse='. $klassenunterrichtliste[$ID]['klasse'].'">
							<button type="button" class="btn btn-info"><span class=" glyphicon glyphicon-list-alt"></span></button>
						   </a>
						   ';
			$linklist[$klassenunterrichtliste[$ID]['klassenunterricht']] [noteneintragslink]= '
							<a href="noteneingabe.php?'.'id='. $klassenunterrichtliste[$ID]['ID'] .'&klassenunterricht=1">
								<button type="button" class="btn btn-primary"><span class="glyphicon glyphicon glyphicon-pencil"></span></button>
							</a>
							';
	}


	//kursliste auswerten und in eine Tabelle fassen: 
	$id_list= array_keys($kursliste);
	foreach($id_list as $ID)
	{
		$linklist[$kursliste[$ID]['langbezeichnung']] [bezeichnung] = $kursliste[$ID]['langbezeichnung'];
		$linklist[$kursliste[$ID]['langbezeichnung']] [schuelerlistenlink] = '
								<a href="kurse.php?'.'id='. $kursliste[$ID]['kurs_ID'] .'">
									<button type="button" class="btn btn-info" width="200"><span class=" glyphicon glyphicon-list-alt"></span></button>
								</a>
								';
		
		$linklist[$kursliste[$ID]['langbezeichnung']] [noteneintragslink] = '
								<a href="noteneingabe.php?'.'id='. $kursliste[$ID]['kurs_ID'] .'&kursunterricht=1">
									<button type="button" class="btn btn-primary"><span class=" glyphicon glyphicon-pencil"></span></button>
								</a>
								';
		 
	};
	asort($linklist);
	//echo "<pre>";
	//print_r($linklist);
	//echo "</pre>";		

	$table .=  '
		<table class="table table-hover" >
			<thead>
			<tr><th>Lerngruppe</th><th>Schülerlisten</th><th>Notenlisten</th></tr>
			</thead>
			';

	
	foreach($linklist as $link)
	{ 
				$table .= '	
					<tr>
						<td>
							'.$link[bezeichnung].': &nbsp;	
						</td>
						<td>
							'.$link[schuelerlistenlink].'	
						</td>
						<td>								
							'.$link[noteneintragslink].'
						</td>
					</tr>
					';
			$table .= '				
			</tr>
			'; 		
	}		
	
	$table .=  '
		</table>';
 
	//#################################### html output zusammenbasteln ################

	echo ('
	<div class="container theme-showcase" role="main">
		<div class="row" >

			<div class="col-sm-6">  	
				'.$table .'
			</div>
			
		</div>
	</div>
	');


}
else
{
	echo ('
	<div class="container theme-showcase" role="main">
		<div class="row">
			<div class="col-sm-6">  
				<h2>Dateneingabe über SchILD gesperrt</h2>
				<a href="index.php">
				<button type="button" class="btn btn-success pull-right">weiter</button>
				</a>	
			</div>
		</div>
	</div>
	');
}

?>


<?php include('includes/footer.inc.php');?>
