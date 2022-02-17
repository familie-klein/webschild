<?php include('includes/header.inc.php'); 
	
	$susIDs = array_keys($_SESSION['liste']);
	

	
	foreach($susIDs as $id)
	{
		//schüler-Daten aus der Mysql-db holen
		try 
		{
			$result = $database->query("SELECT * FROM schueler WHERE ID='" . $id . "'");
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
		//fehlstundenabholen und string basteln ... 
		$gesfehlstd	='';
		$fehlstd = get_fehlstd($database,$id,$_SESSION['schuljahr'],$_SESSION['abschnitt']);		
		if($fehlstd)
		{
			$gesfehlstd	='<small> - '. $fehlstd .' FehlStd</small>';
		}
	
		$fehlstdu = get_fehlstdu($database,$id,$_SESSION['schuljahr'],$_SESSION['abschnitt']);		
		if($fehlstdu)
		{
			$gesfehlstd	.='<small>, davon '. $fehlstdu .' unentschuldigt</small>';
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
	
		$jahrgangsid = get_lernabschnitt_ID($database,$id,$_SESSION['schuljahr'],$_SESSION['abschnitt']); 
		$faecher = get_faecherliste($database,$jahrgangsid);	
		$table = getNotentable($teilnotenart,$faecher);
		
		//html output zusammenbasteln
		
		$htmlbody .= '			
		<div style="page-break-after: always;">
			<div>
				<h2>
					<div class="row"> 
						<div class="col-sm-10">
							'.$vorname. ' '. $nachname .' '. $gesfehlstd .'
						</div>
						<div class="col-sm-2">
							<small class="text-right">
							'. $klasse . ' - '.$_SESSION['schuljahr'].'.'.$_SESSION['abschnitt'].'
							</small>
						</div>
					</div>
				</h2>		
			</div>	
			<div>
				' . $table . '
			</div>
		</div>';	
	}	
		
				
	//$_SESSION['htmlbody'] = $htmlbody;
	//html output zusammensetzen: 
	echo '
	
	
	
	<div class="container theme-showcase" role="main">
		<div class="no-print">
		<div class="well well-sm">		
			<div class="row" > 
				<div class="col-sm-12">
					<h2> Notenübersicht: '.$_SESSION['schuelergruppe'].' 
						<button type="button" class="btn btn-info pull-right" onclick="printThisWindow()" title="drucken"><span class="glyphicon glyphicon-print"></span></button>
					</h2>
				</div>
			</div>
			</div>	
		</div>			
			';
					
	echo $htmlbody;
	
	
	echo '
		<div class="no-print">
		<div class="well well-sm">		
			<div class="row"> 
				<div class="col-sm-12">
					<button type="button" class="btn btn-info pull-right" onclick="printThisWindow()" title="drucken"><span class="glyphicon glyphicon-print"></span></button>
				</div>
			</div>	
		</div>
		</div>			
	</div>			
			';
	

include('includes/footer.inc.php');
?>