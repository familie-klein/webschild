<?php include('includes/header.inc.php'); 

if ($_SESSION['username'] == 'Admin')
			{

if($_POST['mysql-table']) {
	
//'<pre>';

	
	
	$varname=$_POST['item'];
	$value=strtoupper($_POST['note']);
	$id=$_POST['noten_id'];
	$lehrer=$_POST['lehrer'];
	$schuelerJahrgang=$_POST['schuelerJahrgang'];
	
	$teilnoten = array ('KA_1','KA_2','KA_3', 'Somi_1','Somi_2','Somi_Ges','Klausur_1', 'Klausur_2', 'Schr_Ges');




	if (check_input($varname,$value,$schuelerJahrgang)) 
	{

		if (($varname == 'Endnote') )
						{		
							//Sql Befehl wird zusammengesetzt: 
							$sql = "UPDATE `schuelerleistungsdaten` SET `NotenKrz` = '". $value. "' WHERE `schuelerleistungsdaten`.`ID` = ". $id .";";		
									
							//in die Datenbank schreiben:		
							$result = $database -> query($sql);
							//counter für die Rückmeldung:
							$endnoteneintrag++;
						}
						elseif ($varname == 'Fehlstd')
						{
							//Sql Befehl wird zusammengesetzt: 
							$sql = "UPDATE `schuelerleistungsdaten` SET `Fehlstd` = '". $value. "' WHERE `schuelerleistungsdaten`.`ID` = ". $id .";";		
							//in die Datenbank schreiben:		
							$result = $database -> query($sql);
							//counter für die Rückmeldung:
							$fstdeintrag++;
						}
						elseif ($varname == 'uFehlstd')
						{
							//Sql Befehl wird zusammengesetzt: 
							$sql = "UPDATE `schuelerleistungsdaten` SET `uFehlstd` = '". $value. "' WHERE `schuelerleistungsdaten`.`ID` = ". $id .";";		
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
								$sql = "INSERT INTO `webschild_teilnoten` (`id`, `noten_id`, `timecode`, `note`, `notentyp`, `lehrer`) VALUES (NULL, '". $id ."', CURRENT_TIMESTAMP, '". $value. "', '". $varname ."','". $lehrer ."');;";		
								//in die Datenbank schreiben:		
								$result = $database -> query($sql);
								$teilnoteneintrag++;
							}
						}	
		}
		else {
			echo '
				<div class="container theme-showcase" role="main">
				<div class="well well-sm">				
					<h3>Bitte nur Notenkürzel bzw. Zahlen eintragen!<br>Bei Endnoten kann kein Löschungsvermerk gesetzt werden.</h3>
				</div>
				</div>	
				';
			die;
		}
	
	echo '
		<p id="alert"  class="alert alert-success test-center" name="feedback"><i class="glyphicon glyphicon-ok"></i><span id="show"></span>
		Note eingetragen.';			
	echo	'</p>
		';
		
	}

				

if($_GET['id'])
{
	//*****************************************************************************	
	// next und previous in liste ermitteln:
	// array mit allen ids auslesen:
	$liste = array_keys($_SESSION['liste']);
	//pionter auf actuellen Eintrag ausrichten und anzahl der listenelemente finden:
	$pointer = array_search($_GET['id'],$liste);
	//Gesammtanzahl der liste ermitteln
	$anzahl = count ($liste);

	if (($pointer>0) and ($pointer < $anzahl-1) )
	{
		$previous = $liste[($pointer-1)];
		$next = $liste[($pointer+1)];
	}
	elseif ($pointer == '0')
	{
		$previous = $liste[$anzahl-1];
		$next = $liste[($pointer+1)];
	}
	elseif ($pointer == $anzahl-1)	
	{
		$previous = $liste[$pointer-1];
		$next = $liste[0];
	}

	//schüler-Daten aus der Mysql-db holen
	try 
	{
		$result = $database->query("SELECT * FROM Schueler WHERE ID='" .$_GET['id']. "'");
		$obj =  $database->fetchObject ($result);
	}
	catch (DatabaseException $e) 
	{
		echo ("Keine Datenbankabfrage möglich.");
	}

	//Variablen aufbereiten:
	$vorname = ($obj->Vorname);
	$nachname = ($obj->Name);
	$klasse = ($obj->Klasse);	
	if($obj->Jahrgang > 9) {
		$schuelerJahrgang = ($obj->Klasse);
	}
	else {
		$schuelerJahrgang = ($obj->Jahrgang);
	}


	//SI oder SII?
	$SII=array('EF','Q1','Q2','11','12','13');
	if (in_array($klasse, $SII))
	{
		$secI=false;
		$teilnotenart = array ('Somi_1','Somi_2','Somi_Ges','Klausur_1', 'Klausur_2', 'Schr_Ges', 'Endnote', 'Fehlstd', 'uFehlstd');
	}	
	else
	{
		$secI=true;
		$teilnotenart = array ('KA_1','KA_2','KA_3','Endnote', 'Fehlstd', 'uFehlstd');
		
	}	
	//Noten aus der DB abholen:
	
	$jahrgangsid = get_lernabschnitt_ID($database,$_GET['id'],$_SESSION['schuljahr'],$_SESSION['abschnitt']); 
	
	$fehlstd = get_fehlstd($database,$_GET['id'],$_SESSION['schuljahr'],$_SESSION['abschnitt']);		
	if($fehlstd)
	{
		$gesfehlstd	='<small> - '. $fehlstd .' FehlStd</small>';
	}	
	
	$fehlstdu = get_fehlstdu($database,$_GET['id'],$_SESSION['schuljahr'],$_SESSION['abschnitt']);		
	if($fehlstdu)
	{
		$gesfehlstd	.='<small>, davon '. $fehlstdu .' unentschuldigt</small>';
	}		 
	 
	 
	$faecher = get_faecherliste($database,$jahrgangsid);	
	$table = getNotentableadmin($teilnotenart,$faecher,$vorname,$nachname,$_GET['id'],$schuelerJahrgang);

	
	
}
elseif($_GET['jahrgang']) {
	echo '<div class="container theme-showcase" role="main">
	<h2>Jahrgang: '.$_GET['jahrgang'].'</h2>
	';
		//Daten aus der Mysql-db holen
	try 
	{
		$result = $database->query("SELECT * FROM schueler WHERE ASDJahrgang LIKE '".$_GET['jahrgang']."' AND Status LIKE '2' AND Geloescht LIKE '-' ");
	}
	catch (DatabaseException $e) 
	{
		echo ("Keine Datenbankabfrage möglich.");
	}


	
	
	//array zusammenstellen - id als key. 
	while ($obj = $database->fetchObject ($result) ) 
	{	
		$liste[$obj->ID][Name] = $obj->Name;
		$liste[$obj->ID][Vorname] = $obj->Vorname;
		$liste[$obj->ID][Klasse] = $obj->Klasse;		
	}	


	//sortieren des aarys für das erste Erscheinen der Liste in alphabet. Reichenfolge der Nachnamen 
	asort($liste);
	//speichern der liste in der Session variablen für den download und das Weiterklicken bei der individualansicht
	$_SESSION['liste'] = $liste;
	//ersten eintrag in der schülerliste: 
	$id_list= array_keys($liste);

	$jahrgang=$_GET['jahrgang'];
	
	$_SESSION['schuelergruppe'] = 'Stufe ' . $jahrgang .' ('. count($liste) .')' ;
	// html-output zusammenstellen ... 	
	$table = array_to_clickable_table($liste,'leistungsuebersichtadmin.php');
		echo $table;
		

	die;
}
elseif($_GET['klasse'])
{


  	$mysqlquery = "SELECT * FROM schueler WHERE Klasse LIKE '".$_GET['klasse']."' AND Status LIKE '2' AND Geloescht LIKE '-'  ";
	$spalten = array('Telefon','Geschlecht','Geburtsdatum');	
	$liste = schuelerDbAbfrage($database,$mysqlquery,$spalten);
	$liste = array_sort_german2($liste);

	//speichern der liste in der Session variablen für den download und das Weiterklicken bei der individualansicht
	$_SESSION['liste'] = $liste;

			

	$klasse=$_GET['klasse'];

	$_SESSION['schuelergruppe'] = 'Klasse ' . $klasse . ' - ' . $_SESSION['klassenliste'][$klasse][lehrer] . ', ' . $_SESSION['klassenliste'][$klasse][stellv] .' ('. count($liste) .')';
	
	
	//ersten eintrag in der schülerliste: 
		$id_list= array_keys($liste);
	
	
	echo 
	('
	
	
		<div class="container theme-showcase" role="main">
	
		<h2>
			Schülerliste: ' . $_SESSION['schuelergruppe'] . '
		
			<div class="no-print">
			<button type="button" class="btn btn-info pull-right" onclick="printThisWindow()" title="drucken"><span class="glyphicon glyphicon-print"></span></button>
							
		
			<a href="export.php?download=schuelerliste&lerngruppe='. $klasse .'">
			<button type="button" class="btn btn-info pull-right" title="download csv">
			 <span class="glyphicon glyphicon-download"></span></button>
			</a>
			
			<a href="leistungsuebersicht.php?id='. $id_list[0] .'">
			<button type="button" class="btn btn-info pull-right">
			 <span class="glyphicon glyphicon glyphicon-th"></span> </button>
			</a>
			</div>			
		</h2>
		
	');

		$table = array_to_clickable_table($liste,'leistungsuebersichtadmin.php');
		echo '<div class="printarea">';
		echo $table;
		echo '</div>';
	echo 
	('
		</div>
	');
		
}		



elseif ($_GET['noten_id']) {

	$schuelerJahrgang = $_GET['schuelerJahrgang'];		
	
	
	echo '
	<div class="container theme-showcase" role="main">

		<h2>Notenänderung:</h2>

		<form action="leistungsuebersichtadmin.php?id='.$_GET['schueler_id'].'" method="post">

			<input type="hidden" name="mysql-table" value="schuelerleistungsdaten">
			<input type="hidden" name="noten_id" value="'.$_GET['noten_id'].'">
			<input type="hidden" name="item" value="'.$_GET['item'].'">
			<input type="hidden" name="lehrer" value="admin">
			<input type="hidden" name="schuelerJahrgang" value="'. $schuelerJahrgang .'">
				<h3>
						'.$_GET['vorname'].' '.$_GET['nachname'].', '. $_SESSION['faecher_zuordnung'][$_GET['fach']] . ' - '. $_GET['item'] .' : 
						<input type="text" size="3" name="note"  value="' . urlencode($_GET['note']) . '" placeholder="Note">
				</h3>
			<input type="Submit" class="btn btn-success pull-right" value="speichern" />
			

		</form>	

	</div>	
	';
	

	die;
	}
else 
{
	
	echo '<div class="container theme-showcase" role="main">
	
	<div class="no-print">
	<nav aria-label="Page navigation">
			<ul class="pagination">

				';
				foreach ($_SESSION['stufenliste'] as $stufe)
					{						
					echo 
						('
							 <li class="page-item"><a class="page-link" href='.$_SERVER['PHP_SELF'].'?jahrgang=' .$stufe['name'] . '>'. $stufe['name'] .'</a></li>
							');
					}
	echo '			
   	
		</ul>
	</nav>
	</div>
	
	<div class="no-print">
	<nav aria-label="Page navigation">
			<ul class="pagination">

				';
				foreach ($_SESSION['klassenliste'] as $klasse)
					{						
					echo 
						('
							 <li class="page-item"><a class="page-link" href="'.$_SERVER['PHP_SELF'].'?klasse=' .$klasse['name'] . '">'. $klasse['name'] .'</a></li>
							');
					}
				
  echo ' 	
		</ul>
	</nav>
   </div>
<h3>Bitte einzelne Schüler direkt über die Suche auswählen <br>bzw. direkt eine Klasse oder Stufe auswählen.</h3>   
   ';
	

	
	die;
}

//html-output zusammenbasteln: 
	echo '
	<div class="container theme-showcase" role="main">
			<div class="well well-sm">				
			';	

					
					
						echo ('
						   <div class="row"> 
							<div class="col-sm-12">
							 	'); 
							 	
						if ($_SESSION['liste'])	
						{				
							echo ('
								<h2> Notenübersicht:'. $_SESSION['schuelergruppe'] . '
								<a href="'. $_SERVER['PHP_SELF'] .'?id='. $previous .'"><button type="button" class="btn btn-primary"><span class="glyphicon glyphicon-chevron-left"></span></button></a>		
								<a href="'. $_SERVER['PHP_SELF'] .'?id='. $next .'"><button type="button" class="btn btn-primary"><span class="glyphicon glyphicon-chevron-right"></span></button></a>
								</h2>	
							');
						}
						echo ('														
								<h2><a href="schuelerinfo.php?id='. $_GET['id'] . '"> '. $vorname . ' ' . $nachname .	'	</a>'. $gesfehlstd .'			
							   <a href="leistungsuebersichteinzel.php?id='.$_GET['id'].'">
								<button type="button" class="btn btn-info pull-right"><span class="glyphicon glyphicon-pencil"></span> aus</button>
								</a>
								</h2>
							</div>	
							</div>	');					
							
							
				
					
					echo ('			
					 <div class="row"> 
						<div class="col-sm-12">  
							')	;
	
				
					echo ('			
					
							
						</div>
						
					
					');
				
				
				
	echo '					
					
				
				</div>
			</div>	
	';
	
	//###############################################
	

		echo '
			<div class="row"> <div class="col-sm-12">  
				'.$table.'
			</div>	
	';
}
else {
	echo '<h1>Sie sind nicht als Administrator angemeldet!</h1>';
	
}

?>

<?php include('includes/footer.inc.php');
?>
