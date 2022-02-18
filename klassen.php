<?php 
require_once('includes/header.inc.php'); 

// klassenliste al nav-bar erzeugen:
?>


<div class="container theme-showcase" role="main">
	<div class="no-print">
	<nav aria-label="Page navigation">
			<ul class="pagination">

				<?php
				foreach ($_SESSION['klassenliste'] as $klasse)
					{						
					echo 
						('
							 <li class="page-item"><a class="page-link" href="'.$_SERVER['PHP_SELF'].'?klasse=' .$klasse['name'] . '">'. $klasse['name'] .'</a></li>
							');
					}
				?>
   	
		</ul>
	</nav>
   </div>

<?php		

if($_GET['klasse'])
{


  	$mysqlquery = "SELECT * FROM Schueler WHERE Klasse LIKE '".$_GET['klasse']."' AND Status LIKE '2' AND Geloescht LIKE '-'  ";
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
			
			<a href="leistungsuebersichteinzel.php?id='. $id_list[0] .'">
			<button type="button" class="btn btn-info pull-right">
			 <span class="glyphicon glyphicon glyphicon-th"></span> </button>
			</a>
			</div>			
		</h2>
		
	');

		$table = array_to_clickable_table($liste,'leistungsuebersichteinzel.php');
		echo '<div class="printarea">';
		echo $table;
		echo '</div>';
	echo 
	('
		</div>
	');
		
}		
	



require_once ('includes/footer.inc.php');

?>
