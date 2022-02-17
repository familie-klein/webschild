<?php include('includes/header.inc.php'); 



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
		$result = $database->query("SELECT * FROM schueler WHERE ID='" .$_GET['id']. "'");
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
	$table = getNotentable($teilnotenart,$faecher);

	
	
}
else 
{
	echo '<div class="jumbotron"><h2>Keinen Schüler ausgewählt.</h2></div>';
	die;
}

//html-output zusammenbasteln: 
	echo '
	<div class="container theme-showcase" role="main">
			<div class="well well-sm">				
			';	

					
					if ($_SESSION['liste'])	
					{
						echo ('
							<div class="no-print">
						   <div class="row"> 
							<div class="col-sm-12">
							 	<h2> Notenübersicht: ' . $_SESSION['schuelergruppe'] . '
								<a href="'. $_SERVER['PHP_SELF'] .'?id='. $previous .'"><button type="button" class="btn btn-primary"><span class="glyphicon glyphicon-chevron-left"></span></button></a>		
								<a href="'. $_SERVER['PHP_SELF'] .'?id='. $next .'"><button type="button" class="btn btn-primary"><span class="glyphicon glyphicon-chevron-right"></span></button></a>
								<a href="leistungsuebersichtgruppe.php?' . $_SESSION['schuelergruppe'] . '">
								<button type="button" class="btn btn-info pull-right"> ganze Schülergruppe</button>
								</a>								
								</h2>
							</div>	
							</div>						
							</div>
							
											
						');
					}
					
					echo ('			
					 <div class="row"> 
						<div class="col-sm-12">  
							')	;
	
				   
					echo ('			
					
							<h2><a href="schuelerinfo.php?id='. $_GET['id'] . '"> '. $vorname . ' ' . $nachname .	'	</a>'. $gesfehlstd .'				
								<div class="no-print">
								');
						if ($_SESSION['username'] == 'Admin')
						{
							echo ('
							   <a href="leistungsuebersichtadmin.php?id='.$_GET['id'].'">
								<button type="button" class="btn btn-info pull-right"><span class="glyphicon glyphicon-pencil"></span></button>
								</a>
							');			
						}			
								
					echo ('	<button type="button" class="btn btn-info pull-right" onclick="printThisWindow()" title="drucken"><span class="glyphicon glyphicon-print"></span></button>
								</div>
							</h2>
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


	//echo '<pre>';
	//print_r($faecher);
	//echo '</pre>';
	

include('includes/footer.inc.php');
?>
