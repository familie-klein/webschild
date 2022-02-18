<?php 
require_once('includes/header.inc.php'); 

// klassenliste al nav-bar erzeugen:
?>
<div class="container theme-showcase" role="main">
	<div class="no-print">
	<nav aria-label="Page navigation">
			<ul class="pagination">

				<?php
				foreach ($_SESSION['stufenliste'] as $stufe)
					{						
					echo 
						('
							 <li class="page-item"><a class="page-link" href='.$_SERVER['PHP_SELF'].'?jahrgang=' .$stufe['name'] . '>'. $stufe['name'] .'</a></li>
							');
					}
				?>
   	
		</ul>
	</nav>
	</div>

<?php		

if($_GET['jahrgang'])
{


	//Daten aus der Mysql-db holen
	try 
	{
		$result = $database->query("SELECT * FROM Schueler WHERE ASDJahrgang LIKE '".$_GET['jahrgang']."' AND Status LIKE '2' AND Geloescht LIKE '-' ");
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
		$liste[$obj->ID][Telefon] = $obj->Telefon;
		$liste[$obj->ID][Geschlecht] = geschlecht($obj->Geschlecht);
		$liste[$obj->ID][Geburtsdatum] = datums_wandler($obj->Geburtsdatum);

	}	


	//sortieren des aarys für das erste Erscheinen der Liste in alphabet. Reichenfolge der Nachnamen 
	$liste = array_sort_german2($liste);
	//speichern der liste in der Session variablen für den download und das Weiterklicken bei der individualansicht
	$_SESSION['liste'] = $liste;
	//ersten eintrag in der schülerliste: 
		$id_list= array_keys($liste);

	$jahrgang=$_GET['jahrgang'];
	
	$_SESSION['schuelergruppe'] = 'Stufe ' . $jahrgang .' ('. count($liste) .')' ;
	// html-output zusammenstellen ... 	
	echo 
	('
		<div class="container theme-showcase" role="main">
		
		<h2>
			Schülerliste: '. $_SESSION['schuelergruppe'] .'
			<div class="no-print">
			<button type="button" class="btn btn-info pull-right" onclick="printThisWindow()" title="drucken"><span class="glyphicon glyphicon-print"></span></button>			
			
			<a href="export.php?download=schuelerliste&lerngruppe='. $jahrgang .'">
			<button type="button" class="btn btn-info pull-right" title="download csv">
			 <span class="glyphicon glyphicon-download"></span></button>
			</a>
			');
	//if (($_GET['jahrgang'] == 'EF') or ($_GET['jahrgang'] == 'Q1') or ($_GET['jahrgang'] == 'Q2'))
	//{
		echo '
		
			<a href="leistungsuebersichteinzel.php?id='. $id_list[0] .'">
			<button type="button" class="btn btn-info pull-right" title="Leistungsübersicht"> 
			 <span class="glyphicon glyphicon glyphicon-th"></span></button>
			</a>		
					</div>
		';	
	//}	
	
	echo 
	('		
		</h2>
	
	');

		$table = array_to_clickable_table($liste,'leistungsuebersichteinzel.php');
		echo $table;
	echo 
	('
		</div>
	');
		
}		
	



require_once ('includes/footer.inc.php');

?>
