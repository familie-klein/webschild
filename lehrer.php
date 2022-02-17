<?php 
require_once ('includes/header.inc.php'); 


	//Daten aus der Mysql-db holen
	try 
	{
		//warum auch immer lässt er nur eine Abfrageauswertung zu ... 
		$result = $database->query("SELECT * FROM k_lehrer WHERE sichtbar='+'");
		$result2 = $database->query("SELECT * FROM k_lehrer WHERE sichtbar='+'");
	}
	catch (DatabaseException $e) 
	{
		echo ("Keine Datenbankabfrage möglich.");
	}
	
	//maximale Fächerzahl zählen
	$maxFaecherZahl = 0; 
	while ($obj2 = $database->fetchObject ($result2) ) 
	{	
		$i=0;		
		$fachkombi = explode(',',$obj2->Faecher);
		foreach ($fachkombi as $fach)
		{
			$i++;		
			$item = trim($fach);
			$faecherUebersicht[$item]++;
			if ($i>$maxFaecherZahl)
			{
				$maxFaecherZahl++;
			}
		}
	}
	
	
	//array zusammenstellen - id als key. 
	while ($obj = $database->fetchObject ($result) ) 
	{	
		if(!$_GET['faecher'])
		{
			$liste[$obj->ID][Name] = $obj->Nachname;
			$liste[$obj->ID][Vorname] = $obj->Vorname;
			$liste[$obj->ID][Kürzel] = $obj->Kuerzel;	
			$liste[$obj->ID][email] = $obj->EMailDienstlich;
			$liste[$obj->ID][Telefon] = $obj->Tel;
			$liste[$obj->ID][mobil] = $obj->Handy;
				
			//fächerauflisten und Kollegen zählen
			$fachkombi = explode(',',$obj->Faecher);

			for($i=0; $i < $maxFaecherZahl; $i++)
			{
				$j= $i + 1;
				$liste[$obj->ID]['Fach' . $j] =  $fachkombi[$i];
			}
		}
		else 
		{
			$fachkombi = explode(',',$obj->Faecher);	
		   if (in_array($_GET['faecher'],$fachkombi))
		   {
				$liste[$obj->ID][Name] = $obj->Nachname;
				$liste[$obj->ID][Vorname] = $obj->Vorname;
				$liste[$obj->ID][Kürzel] = $obj->Kuerzel;	
				$liste[$obj->ID][email] = $obj->EMailDienstlich;
				$liste[$obj->ID][Telefon] = $obj->Tel;
				$liste[$obj->ID][mobil] = $obj->Handy;
				for($i=0; $i < $maxFaecherZahl; $i++)
				{
					$j= $i + 1;
					$liste[$obj->ID]['Fach' . $j] =  $fachkombi[$i];
				}
				
		   }
		}		
	}
		




	//sortieren des aarys für das erste Erscheinen der Liste in alphabet. Reichenfolge der Nachnamen bzw. Kürzel
	if ($_GET['sortierung'] == 'kuerzel')
	{
		$liste = array_sort_kuerzel($liste);	
	}
	else 
	{
		$liste = array_sort_german2($liste);
	}
	
	//speichern der Lehrerliste in der Session variablen für den download
	$_SESSION['liste'] = $liste;


	
	// html-output zusammenstellen ... 	
	echo 
	('
		<div class="container theme-showcase" role="main">
		<div class="no-print">
		<h2>
			Lehrerliste 
			<button type="button" class="btn btn-info pull-right" onclick="printThisWindow()" title="drucken"><span class="glyphicon glyphicon-print"></span></button>
						
			<a href="export.php?download=lehrerliste">
			<button type="button" class="btn btn-info pull-right" title="download csv">
			 <span class="glyphicon glyphicon-download"></span></button>
			</a>
	
			<a href="mailto:name@bla.de?bcc=name2@beispielwebseite.de">
				<button type="button" class="btn btn-info pull-right">
					<span class="glyphicon glyphicon-envelope" title="mail schreiben">
					</span>
				</button>
			</a>				
	
	
		</h2>	
		</div>
	');

	if ($_GET[sortierung]=='kuerzel')
	{
		$checked_kuerzel = 'checked';
	}
	else
	{
		$checked_namen = 'checked';
	}	

	if (!$_GET['faecher'])
	{
		$select_all='selected';
	}

	echo 
	('
	<div class="no-print">
	<form method="get" id="itemlist" class="pull-right" role="form" action=' . $_SERVER['PHP_SELF'] . '>
		<select id="faecher" name="faecher">
    		<option value="" '. $select_all .' >alle Fächer</option>
			');

			$faecherkeys= array_keys($faecherUebersicht);			

			foreach ($faecherkeys as $fach)
			{
				echo ('
				<option value="'.$fach.'" ');
				if (($_GET['faecher'] == $fach) and ($fach != '')) 
				{
					echo ' selected ';
				}	
				 echo ('>'.$fach.' ('. $faecherUebersicht[$fach] .')</option> 
				');
			}
	echo
	('
  		</select>
		<input type="radio" id="namen" name="sortierung" value="namen" '. $checked_namen .'>
  			<label for="namen">Namen </label>
  			<input type="radio" id="kuerzel" name="sortierung" value="kuerzel"'. $checked_kuerzel .'>
 			<label for="kuerzel">Kürzel </label> &nbsp;
		<button type="submit" name="itemlist" value="1" class="btn btn-info">sortieren</button>
	</form>
	</div>
	
	');

		$table = array_to_clickable_table($liste,'lehrerinfo.php');
		echo $table;
	echo 
	('
		</div>
	');	
	
require_once ('includes/footer.inc.php');
?>
